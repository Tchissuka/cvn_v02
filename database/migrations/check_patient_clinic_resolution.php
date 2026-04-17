<?php

$pdo = new PDO('mysql:host=localhost;dbname=cvnsu_vbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queries = [
    'unassigned_patients' => "SELECT COUNT(*) FROM patients WHERE clinic_id = 900000001",
    'resolved_log_rows' => "SELECT COUNT(*) FROM migration_patient_clinic_resolution_log",
    'resolved_by_rule' => "SELECT resolution_rule, COUNT(*) FROM migration_patient_clinic_resolution_log GROUP BY resolution_rule ORDER BY COUNT(*) DESC, resolution_rule ASC",
    'top_patient_clinics' => "SELECT clinic_id, COUNT(*) FROM patients GROUP BY clinic_id ORDER BY COUNT(*) DESC LIMIT 10"
];

echo "PATIENT_CLINIC_SANITY\n";
echo 'unassigned_patients:' . $pdo->query($queries['unassigned_patients'])->fetchColumn() . PHP_EOL;
echo 'resolved_log_rows:' . $pdo->query($queries['resolved_log_rows'])->fetchColumn() . PHP_EOL;

echo PHP_EOL . "RESOLVED_BY_RULE\n";
foreach ($pdo->query($queries['resolved_by_rule'], PDO::FETCH_NUM) as $row) {
    echo implode(':', $row) . PHP_EOL;
}

echo PHP_EOL . "TOP_PATIENT_CLINICS\n";
foreach ($pdo->query($queries['top_patient_clinics'], PDO::FETCH_NUM) as $row) {
    echo implode(':', $row) . PHP_EOL;
}