<?php

namespace Source\Models\Billing;

use Source\Core\Connect;
use Source\Core\Model;

class Payment extends Model
{
    public function __construct()
    {
        parent::__construct('payments', [], []);
    }

    public function recentByPatient(int $clinicId, int $patientId, int $limit = 8): array
    {
        if (!$this->tableExists('payments') || !$this->tableExists('invoices')) {
            return [];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return [];
        }

        $stmt = $pdo->prepare(
            "SELECT
                p.*,
                i.invoice_number,
                i.status AS invoice_status
             FROM payments p
             INNER JOIN invoices i ON i.Id = p.invoice_id AND i.clinic_id = p.clinic_id
             WHERE p.clinic_id = :clinic AND i.patient_id = :patient
             ORDER BY p.payment_date DESC, p.Id DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':clinic', $clinicId, \PDO::PARAM_INT);
        $stmt->bindValue(':patient', $patientId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ) ?: [];
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
