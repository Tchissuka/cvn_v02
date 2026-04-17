<?php

namespace Source\Models\Clinical;

use Source\Core\Connect;
use Source\Models\ClinicModel;

class Doctor extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('doctors', ['Id'], ['clinic_id', 'person_id']);
    }

    public function bootstrap(
        int $clinicId,
        int $personId,
        ?int $specialtyId = null,
        ?int $departmentId = null,
        ?string $registrationNumber = null,
        bool $active = true,
        ?string $notes = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->person_id = $personId;
        $this->specialty_id = $specialtyId;
        $this->department_id = $departmentId;
        $this->registration_number = $registrationNumber;
        $this->is_active = $active ? 1 : 0;
        $this->notes = $notes;
        return $this;
    }

    public function saveDoctor(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Preencha os dados obrigatórios do médico.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o médico, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Médico salvo com sucesso.");
        return true;
    }

    public function catalogReady(): bool
    {
        return $this->tableExists('doctors');
    }

    public function listActiveByClinic(int $clinicId, ?string $search = null, int $limit = 30): array
    {
        if (!$this->catalogReady()) {
            return [];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return [];
        }

        $personSource = $this->personSource();
        $where = 'd.clinic_id = :clinic AND COALESCE(d.is_active, 1) = 1';
        $params = ['clinic' => $clinicId];
        $limit = max(1, $limit);

        if ($search) {
            $where .= ' AND (' . $personSource['search'] . ')';
            $params['search'] = "%{$search}%";
        }

        $stmt = $pdo->prepare(
            "SELECT
                d.*,
                {$personSource['name']} AS doctor_name,
                {$personSource['contact']} AS doctor_contact
             FROM doctors d
             {$personSource['join']}
             WHERE {$where}
             ORDER BY doctor_name ASC, d.Id ASC
             LIMIT :limit"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ) ?: [];
    }

    private function personSource(): array
    {
        if ($this->tableExists('people')) {
            return [
                'join' => 'LEFT JOIN people pe ON pe.id = d.person_id AND pe.clinic_id = d.clinic_id',
                'name' => "COALESCE(NULLIF(pe.full_name, ''), CONCAT('Médico #', d.person_id))",
                'contact' => "COALESCE(NULLIF(pe.phone, ''), NULLIF(pe.mobile, ''), 'Sem contacto')",
                'search' => "COALESCE(pe.full_name, '') LIKE :search OR COALESCE(pe.phone, '') LIKE :search OR COALESCE(pe.mobile, '') LIKE :search"
            ];
        }

        if ($this->tableExists('personal')) {
            return [
                'join' => 'LEFT JOIN personal pe ON pe.Id = d.person_id',
                'name' => "COALESCE(NULLIF(pe.full_name, ''), CONCAT('Médico #', d.person_id))",
                'contact' => "'Sem contacto'",
                'search' => "COALESCE(pe.full_name, '') LIKE :search"
            ];
        }

        return [
            'join' => '',
            'name' => "CONCAT('Médico #', d.person_id)",
            'contact' => "'Sem contacto'",
            'search' => "CAST(d.person_id AS CHAR) LIKE :search"
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
}
