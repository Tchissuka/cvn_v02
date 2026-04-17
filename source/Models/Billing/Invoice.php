<?php

namespace Source\Models\Billing;

use Source\Core\Connect;
use Source\Models\ClinicModel;

class Invoice extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('invoices', ['Id'], ['clinic_id', 'invoice_number', 'invoice_date', 'status']);
    }

    public function bootstrap(
        int $clinicId,
        string $invoiceNumber,
        string $invoiceDate,
        string $status,
        ?int $patientId = null,
        ?int $insuranceId = null,
        float $totalGross = 0.0,
        float $totalDiscount = 0.0,
        float $totalNet = 0.0,
        ?string $notes = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->invoice_number = trim($invoiceNumber);
        $this->invoice_date = $invoiceDate;
        $this->status = $status;
        $this->patient_id = $patientId;
        $this->insurance_id = $insuranceId;
        $this->total_gross = $totalGross;
        $this->total_discount = $totalDiscount;
        $this->total_net = $totalNet;
        $this->notes = $notes;
        return $this;
    }

    public function saveInvoice(): bool
    {
        if (!$this->required() || empty($this->invoice_number)) {
            $this->message->warning("Preencha os dados obrigatórios da fatura (número, data, estado e clínica).");
            return false;
        }

        $terms = "clinic_id = :c AND invoice_number = :n" . (!empty($this->Id) ? " AND Id != :id" : "");
        $params = "c={$this->clinic_id}&n={$this->invoice_number}" . (!empty($this->Id) ? "&id={$this->Id}" : "");
        if ($this->find($terms, $params, 'Id')->fetch()) {
            $this->message->warning("Já existe uma fatura com este número nesta clínica.");
            return false;
        }

        if ($this->total_net <= 0) {
            $this->message->warning("O valor líquido da fatura deve ser maior que zero.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar a fatura, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Fatura salva com sucesso.");
        return true;
    }

    public function summarizeByPatient(int $clinicId, int $patientId): object
    {
        if (!$this->billingTablesReady()) {
            return (object)[
                'total_invoices' => 0,
                'draft_invoices' => 0,
                'open_invoices' => 0,
                'settled_invoices' => 0,
                'total_invoiced' => 0.0,
                'total_paid' => 0.0,
                'total_outstanding' => 0.0
            ];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return (object)[
                'total_invoices' => 0,
                'draft_invoices' => 0,
                'open_invoices' => 0,
                'settled_invoices' => 0,
                'total_invoiced' => 0.0,
                'total_paid' => 0.0,
                'total_outstanding' => 0.0
            ];
        }

        $stmt = $pdo->prepare(
            "SELECT
                COUNT(*) AS total_invoices,
                COALESCE(SUM(CASE WHEN i.status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_invoices,
                COALESCE(SUM(CASE WHEN i.status <> 'draft' AND GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) > 0 THEN 1 ELSE 0 END), 0) AS open_invoices,
                COALESCE(SUM(CASE WHEN GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) = 0 AND i.total_net > 0 THEN 1 ELSE 0 END), 0) AS settled_invoices,
                COALESCE(SUM(i.total_net), 0) AS total_invoiced,
                COALESCE(SUM(COALESCE(pay.total_paid, 0)), 0) AS total_paid,
                COALESCE(SUM(GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0)), 0) AS total_outstanding
             FROM invoices i
             LEFT JOIN (
                SELECT invoice_id, clinic_id, SUM(amount) AS total_paid
                FROM payments
                GROUP BY invoice_id, clinic_id
             ) pay ON pay.invoice_id = i.Id AND pay.clinic_id = i.clinic_id
             WHERE i.clinic_id = :clinic AND i.patient_id = :patient"
        );
        $stmt->execute([
            'clinic' => $clinicId,
            'patient' => $patientId
        ]);

        return $stmt->fetch(\PDO::FETCH_OBJ) ?: (object)[
            'total_invoices' => 0,
            'draft_invoices' => 0,
            'open_invoices' => 0,
            'settled_invoices' => 0,
            'total_invoiced' => 0.0,
            'total_paid' => 0.0,
            'total_outstanding' => 0.0
        ];
    }

    public function recentByPatient(int $clinicId, int $patientId, int $limit = 8): array
    {
        if (!$this->billingTablesReady()) {
            return [];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return [];
        }

        $stmt = $pdo->prepare(
            "SELECT
                i.*,
                COALESCE(pay.total_paid, 0) AS total_paid,
                GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) AS outstanding_balance
             FROM invoices i
             LEFT JOIN (
                SELECT invoice_id, clinic_id, SUM(amount) AS total_paid
                FROM payments
                GROUP BY invoice_id, clinic_id
             ) pay ON pay.invoice_id = i.Id AND pay.clinic_id = i.clinic_id
             WHERE i.clinic_id = :clinic AND i.patient_id = :patient
             ORDER BY i.invoice_date DESC, i.Id DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':clinic', $clinicId, \PDO::PARAM_INT);
        $stmt->bindValue(':patient', $patientId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ) ?: [];
    }

    public function summarizeByClinic(int $clinicId): object
    {
        if (!$this->billingTablesReady()) {
            return (object)[
                'total_invoices' => 0,
                'draft_invoices' => 0,
                'open_invoices' => 0,
                'settled_invoices' => 0,
                'total_invoiced' => 0.0,
                'total_paid' => 0.0,
                'total_outstanding' => 0.0
            ];
        }

        $pdo = Connect::getInstance();
        $stmt = $pdo->prepare(
            "SELECT
                COUNT(*) AS total_invoices,
                COALESCE(SUM(CASE WHEN i.status = 'draft' THEN 1 ELSE 0 END), 0) AS draft_invoices,
                COALESCE(SUM(CASE WHEN i.status <> 'draft' AND GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) > 0 THEN 1 ELSE 0 END), 0) AS open_invoices,
                COALESCE(SUM(CASE WHEN GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) = 0 AND i.total_net > 0 THEN 1 ELSE 0 END), 0) AS settled_invoices,
                COALESCE(SUM(i.total_net), 0) AS total_invoiced,
                COALESCE(SUM(COALESCE(pay.total_paid, 0)), 0) AS total_paid,
                COALESCE(SUM(GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0)), 0) AS total_outstanding
             FROM invoices i
             LEFT JOIN (
                SELECT invoice_id, clinic_id, SUM(amount) AS total_paid
                FROM payments
                GROUP BY invoice_id, clinic_id
             ) pay ON pay.invoice_id = i.Id AND pay.clinic_id = i.clinic_id
             WHERE i.clinic_id = :clinic"
        );
        $stmt->execute(['clinic' => $clinicId]);

        return $stmt->fetch(\PDO::FETCH_OBJ) ?: (object)[
            'total_invoices' => 0,
            'draft_invoices' => 0,
            'open_invoices' => 0,
            'settled_invoices' => 0,
            'total_invoiced' => 0.0,
            'total_paid' => 0.0,
            'total_outstanding' => 0.0
        ];
    }

    public function recentByClinic(int $clinicId, int $limit = 12, ?string $search = null): array
    {
        if (!$this->billingTablesReady()) {
            return [];
        }

        $pdo = Connect::getInstance();
        $limit = max(1, $limit);
        $params = ['clinic' => $clinicId];
        $searchSql = '';

        if ($search) {
            $searchSql = ' AND (i.invoice_number LIKE :search OR COALESCE(i.status, "") LIKE :search OR COALESCE(i.notes, "") LIKE :search)';
            $params['search'] = "%{$search}%";
        }

        $stmt = $pdo->prepare(
            "SELECT
                i.*,
                COALESCE(pay.total_paid, 0) AS total_paid,
                GREATEST(i.total_net - COALESCE(pay.total_paid, 0), 0) AS outstanding_balance
             FROM invoices i
             LEFT JOIN (
                SELECT invoice_id, clinic_id, SUM(amount) AS total_paid
                FROM payments
                GROUP BY invoice_id, clinic_id
             ) pay ON pay.invoice_id = i.Id AND pay.clinic_id = i.clinic_id
             WHERE i.clinic_id = :clinic{$searchSql}
             ORDER BY i.invoice_date DESC, i.Id DESC
             LIMIT :limit"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ) ?: [];
    }

    public function searchByClinic(int $clinicId, string $search, int $limit = 8): array
    {
        return $this->recentByClinic($clinicId, $limit, $search);
    }

    public function deleteDraftCascade(int $clinicId, int $invoiceId): bool
    {
        if (!$this->billingTablesReady()) {
            $this->message->warning('A fundação financeira ainda não está disponível nesta base.');
            return false;
        }

        $invoice = $this->findByIdInClinic($invoiceId, $clinicId);
        if (!$invoice) {
            $this->message->warning('Rascunho não encontrado para exclusão.');
            return false;
        }

        if (($invoice->status ?? null) !== 'draft') {
            $this->message->warning('Só é permitido eliminar documentos ainda em rascunho.');
            return false;
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            $this->message->error('Ligação à base de dados indisponível para excluir o rascunho.');
            return false;
        }

        try {
            $paymentCount = $pdo->prepare(
                'SELECT COUNT(*) FROM payments WHERE clinic_id = :clinic AND invoice_id = :invoice'
            );
            $paymentCount->execute([
                'clinic' => $clinicId,
                'invoice' => $invoiceId
            ]);

            if ((int)$paymentCount->fetchColumn() > 0) {
                $this->message->warning('Este rascunho já possui pagamentos associados e não pode ser eliminado.');
                return false;
            }

            $pdo->beginTransaction();

            $deleteItems = $pdo->prepare(
                'DELETE FROM invoice_items WHERE clinic_id = :clinic AND invoice_id = :invoice'
            );
            $deleteItems->execute([
                'clinic' => $clinicId,
                'invoice' => $invoiceId
            ]);

            $deleteInvoice = $pdo->prepare(
                'DELETE FROM invoices WHERE clinic_id = :clinic AND Id = :invoice AND status = :status'
            );
            $deleteInvoice->execute([
                'clinic' => $clinicId,
                'invoice' => $invoiceId,
                'status' => 'draft'
            ]);

            if ($deleteInvoice->rowCount() < 1) {
                $pdo->rollBack();
                $this->message->warning('O rascunho não pôde ser eliminado porque já mudou de estado.');
                return false;
            }

            $pdo->commit();
            $this->message->success('Rascunho eliminado com sucesso.');
            return true;
        } catch (\PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->fail = $exception;
            $this->message->error('Não foi possível eliminar o rascunho selecionado.');
            return false;
        }
    }

    public function billingTablesReady(): bool
    {
        return $this->tableExists('invoices') && $this->tableExists('payments');
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
