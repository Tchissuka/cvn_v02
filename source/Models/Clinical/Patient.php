<?php

namespace Source\Models\Clinical;

use Source\Core\Connect;
use Source\Models\ClinicModel;

class Patient extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('patients', ['Id'], ['clinic_id', 'person_id', 'medical_record_number']);
    }

    public function bootstrap(
        int $clinicId,
        int $personId,
        string $medicalRecordNumber,
        ?int $insuranceId = null,
        ?string $notes = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->person_id = $personId;
        $registryColumn = $this->registryColumn();
        $this->{$registryColumn} = trim($medicalRecordNumber);

        if ($this->columnExists('patients', 'insurance_id')) {
            $this->insurance_id = $insuranceId;
        }

        $this->notes = $notes;
        return $this;
    }

    public function savePatient(): bool
    {
        $registryColumn = $this->registryColumn();
        $registryValue = trim((string)($this->{$registryColumn} ?? ''));

        if (empty($this->clinic_id) || empty($this->person_id) || $registryValue === '') {
            $this->message->warning("Preencha os dados obrigatórios do paciente.");
            return false;
        }

        if ($registryValue !== '') {
            $terms = "clinic_id = :c AND {$registryColumn} = :mrn" . (!empty($this->Id) ? " AND Id != :id" : "");
            $params = "c={$this->clinic_id}&mrn={$registryValue}" . (!empty($this->Id) ? "&id={$this->Id}" : "");
            if ($this->find($terms, $params, 'Id')->fetch()) {
                $this->message->warning("Já existe um paciente com este número de processo nesta clínica.");
                return false;
            }
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o paciente, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Paciente salvo com sucesso.");
        return true;
    }

    public function paginateRegistryByClinic(int $clinicId, int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return [
                'data' => [],
                'total' => 0,
                'page' => $page,
                'pages' => 0,
                'per_page' => $perPage
            ];
        }

        $personSource = $this->personSource();
        $registryColumn = $this->registryColumn();
        $insuranceSelect = $this->columnExists('patients', 'insurance_id') ? 'p.insurance_id' : 'NULL';
        $where = 'p.clinic_id = :clinic';
        $params = ['clinic' => $clinicId];

        if ($search) {
            $searchTerms = [
                "COALESCE(p.{$registryColumn}, '') LIKE :search",
                'CAST(p.person_id AS CHAR) LIKE :search'
            ];

            if (!empty($personSource['search'])) {
                $searchTerms[] = $personSource['search'];
            }

            $where .= ' AND (' . implode(' OR ', $searchTerms) . ')';
            $params['search'] = "%{$search}%";
        }

        $countStmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM patients p
             {$personSource['join']}
             WHERE {$where}"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT
                p.*,
                p.{$registryColumn} AS medical_record_number,
                {$insuranceSelect} AS insurance_id,
                {$personSource['name']} AS patient_name,
                {$personSource['contact']} AS patient_contact,
                {$personSource['email']} AS patient_email,
                {$personSource['address']} AS patient_address
             FROM patients p
             {$personSource['join']}
             WHERE {$where}
             ORDER BY p.created_at DESC, p.Id DESC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(\PDO::FETCH_OBJ) ?: [],
            'total' => $total,
            'page' => $page,
            'pages' => (int)ceil($total / $perPage),
            'per_page' => $perPage
        ];
    }

    public function findRegistryByIdInClinic(int $patientId, int $clinicId): ?object
    {
        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return null;
        }

        $personSource = $this->personSource();
        $registryColumn = $this->registryColumn();
        $insuranceSelect = $this->columnExists('patients', 'insurance_id') ? 'p.insurance_id' : 'NULL';
        $stmt = $pdo->prepare(
            "SELECT
                p.*,
            p.{$registryColumn} AS medical_record_number,
            {$insuranceSelect} AS insurance_id,
                {$personSource['name']} AS patient_name,
                {$personSource['contact']} AS patient_contact,
                {$personSource['email']} AS patient_email,
                {$personSource['address']} AS patient_address
             FROM patients p
             {$personSource['join']}
             WHERE p.clinic_id = :clinic AND p.Id = :patient
             LIMIT 1"
        );
        $stmt->execute([
            'clinic' => $clinicId,
            'patient' => $patientId
        ]);

        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    private function registryColumn(): string
    {
        return $this->columnExists('patients', 'medical_record_number') ? 'medical_record_number' : 'regist_number';
    }

    private function personSource(): array
    {
        if ($this->tableExists('people')) {
            return [
                'join' => 'LEFT JOIN people pe ON pe.id = p.person_id AND pe.clinic_id = p.clinic_id',
                'name' => "COALESCE(NULLIF(pe.full_name, ''), CONCAT('Pessoa #', p.person_id))",
                'contact' => "COALESCE(NULLIF(pe.phone, ''), NULLIF(pe.mobile, ''), 'Sem contacto')",
                'email' => 'pe.email',
                'address' => 'pe.address',
                'search' => "COALESCE(pe.full_name, '') LIKE :search OR COALESCE(pe.phone, '') LIKE :search OR COALESCE(pe.mobile, '') LIKE :search"
            ];
        }

        if ($this->tableExists('personal')) {
            return [
                'join' => 'LEFT JOIN personal pe ON pe.Id = p.person_id',
                'name' => "COALESCE(NULLIF(pe.full_name, ''), CONCAT('Pessoa #', p.person_id))",
                'contact' => "'Sem contacto'",
                'email' => 'NULL',
                'address' => 'NULL',
                'search' => "COALESCE(pe.full_name, '') LIKE :search"
            ];
        }

        return [
            'join' => '',
            'name' => "CONCAT('Pessoa #', p.person_id)",
            'contact' => "'Sem contacto'",
            'email' => 'NULL',
            'address' => 'NULL',
            'search' => ''
        ];
    }

    private function tableExists(string $table): bool
    {
        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return false;
        }

        $stmt = $pdo->prepare(
            'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table LIMIT 1'
        );
        $stmt->execute(['table' => $table]);

        return (bool)$stmt->fetchColumn();
    }

    private function columnExists(string $table, string $column): bool
    {
        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return false;
        }

        $stmt = $pdo->prepare(
            'SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column LIMIT 1'
        );
        $stmt->execute([
            'table' => $table,
            'column' => $column
        ]);

        return (bool)$stmt->fetchColumn();
    }
}