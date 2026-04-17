SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SET @CLINIC_UNASSIGNED_ID := 900000001;

CREATE TABLE IF NOT EXISTS migration_patient_clinic_resolution_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_id BIGINT NOT NULL,
    person_id BIGINT NOT NULL,
    old_clinic_id BIGINT NOT NULL,
    new_clinic_id BIGINT NOT NULL,
    resolution_rule VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_patient_resolution (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TEMPORARY TABLE IF EXISTS tmp_patient_clinic_candidates;
CREATE TEMPORARY TABLE tmp_patient_clinic_candidates (
    patient_id BIGINT NOT NULL,
    person_id BIGINT NOT NULL,
    candidate_clinic_id BIGINT NOT NULL,
    resolution_rule VARCHAR(100) NOT NULL,
    priority INT NOT NULL,
    KEY idx_tmp_patient_candidates (patient_id, priority, candidate_clinic_id)
) ENGINE=InnoDB;

INSERT INTO tmp_patient_clinic_candidates (patient_id, person_id, candidate_clinic_id, resolution_rule, priority)
SELECT DISTINCT p.id, p.person_id, e.id_institui, 'employee_institution', 10
FROM patients p
INNER JOIN cvnsu_bd.employees e ON e.id_person = p.person_id
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
  AND e.id_institui IS NOT NULL
  AND e.id_institui > 0;

INSERT INTO tmp_patient_clinic_candidates (patient_id, person_id, candidate_clinic_id, resolution_rule, priority)
SELECT DISTINCT p.id, p.person_id, u.instit, 'user_institution', 20
FROM patients p
INNER JOIN cvnsu_bd.users u ON u.Id = p.person_id
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
  AND u.instit IS NOT NULL
  AND u.instit > 0;

INSERT INTO tmp_patient_clinic_candidates (patient_id, person_id, candidate_clinic_id, resolution_rule, priority)
SELECT DISTINCT p.id, p.person_id, u.instit, 'consultation_created_by_institution', 30
FROM patients p
INNER JOIN cvnsu_bd.consultation c ON c.person_id = p.person_id
INNER JOIN cvnsu_bd.users u ON u.Id = c.created_by
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
  AND u.instit IS NOT NULL
  AND u.instit > 0;

INSERT INTO tmp_patient_clinic_candidates (patient_id, person_id, candidate_clinic_id, resolution_rule, priority)
SELECT DISTINCT p.id, p.person_id, e.id_institui, 'consultation_doctor_institution', 40
FROM patients p
INNER JOIN cvnsu_bd.consultation c ON c.person_id = p.person_id
INNER JOIN cvnsu_bd.employees e ON e.id_person = c.doctor_id
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
  AND e.id_institui IS NOT NULL
  AND e.id_institui > 0;

INSERT INTO tmp_patient_clinic_candidates (patient_id, person_id, candidate_clinic_id, resolution_rule, priority)
SELECT DISTINCT p.id, p.person_id, e.id_institui, 'consultation_triage_institution', 50
FROM patients p
INNER JOIN cvnsu_bd.consultation c ON c.person_id = p.person_id
INNER JOIN cvnsu_bd.employees e ON e.id_person = c.triagem_tec_id
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
  AND e.id_institui IS NOT NULL
  AND e.id_institui > 0;

DROP TEMPORARY TABLE IF EXISTS tmp_patient_clinic_priority_stats;
CREATE TEMPORARY TABLE tmp_patient_clinic_priority_stats AS
SELECT
    c.patient_id,
    MIN(c.person_id) AS person_id,
    c.priority,
    MIN(c.candidate_clinic_id) AS candidate_clinic_id,
    MIN(c.resolution_rule) AS resolution_rule,
    COUNT(DISTINCT c.candidate_clinic_id) AS candidate_clinic_count
FROM tmp_patient_clinic_candidates c
GROUP BY c.patient_id, c.priority
HAVING COUNT(DISTINCT c.candidate_clinic_id) = 1;

DROP TEMPORARY TABLE IF EXISTS tmp_patient_clinic_resolved;
CREATE TEMPORARY TABLE tmp_patient_clinic_resolved AS
SELECT
    s.patient_id,
    s.person_id,
    s.candidate_clinic_id,
    s.resolution_rule,
    s.priority
FROM tmp_patient_clinic_priority_stats s
INNER JOIN (
    SELECT patient_id, MIN(priority) AS chosen_priority
    FROM tmp_patient_clinic_priority_stats
    GROUP BY patient_id
) chosen
    ON chosen.patient_id = s.patient_id
   AND chosen.chosen_priority = s.priority;

INSERT INTO migration_patient_clinic_resolution_log (
    patient_id,
    person_id,
    old_clinic_id,
    new_clinic_id,
    resolution_rule
)
SELECT
    p.id,
    p.person_id,
    p.clinic_id,
    r.candidate_clinic_id,
    r.resolution_rule
FROM patients p
INNER JOIN tmp_patient_clinic_resolved r ON r.patient_id = p.id
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID
ON DUPLICATE KEY UPDATE
    new_clinic_id = VALUES(new_clinic_id),
    resolution_rule = VALUES(resolution_rule),
    created_at = CURRENT_TIMESTAMP;

UPDATE patients p
INNER JOIN tmp_patient_clinic_resolved r ON r.patient_id = p.id
SET p.clinic_id = r.candidate_clinic_id,
    p.updated_at = CURRENT_TIMESTAMP
WHERE p.clinic_id = @CLINIC_UNASSIGNED_ID;

UPDATE people pe
INNER JOIN patients p ON p.person_id = pe.id
SET pe.clinic_id = p.clinic_id,
    pe.updated_at = CURRENT_TIMESTAMP
WHERE pe.clinic_id = @CLINIC_UNASSIGNED_ID
  AND p.clinic_id <> @CLINIC_UNASSIGNED_ID;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'resolved_patients' AS metric, COUNT(*) AS total
FROM migration_patient_clinic_resolution_log
UNION ALL
SELECT 'remaining_unassigned_patients' AS metric, COUNT(*) AS total
FROM patients
WHERE clinic_id = @CLINIC_UNASSIGNED_ID;