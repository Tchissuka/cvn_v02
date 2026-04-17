<?php

namespace Source\Models\Billing;

use Source\Core\Connect;
use Source\Models\ClinicModel;

class Service extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('services', ['Id'], ['clinic_id', 'name']);
    }

    public function bootstrap(
        int $clinicId,
        string $name,
        ?string $code = null,
        ?string $description = null,
        float $defaultPrice = 0.0,
        bool $active = true
    ): self {
        $this->clinic_id = $clinicId;
        $this->name = trim($name);
        $this->code = $code;
        $this->description = $description;
        $this->default_price = $defaultPrice;
        $this->is_active = $active ? 1 : 0;
        return $this;
    }

    public function saveService(): bool
    {
        if (!$this->required() || empty($this->name)) {
            $this->message->warning("Informe o nome do serviço e a clínica.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o serviço, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Serviço salvo com sucesso.");
        return true;
    }

    public function catalogReady(): bool
    {
        return $this->tableExists('services');
    }

    public function paginateCatalogByClinic(int $clinicId, int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        if (!$this->catalogReady()) {
            return [
                'data' => [],
                'total' => 0,
                'page' => max(1, $page),
                'pages' => 0,
                'per_page' => max(1, $perPage)
            ];
        }

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

        $where = 'clinic_id = :clinic';
        $params = ['clinic' => $clinicId];

        if ($search) {
            $where .= ' AND (name LIKE :search OR COALESCE(code, "") LIKE :search OR COALESCE(description, "") LIKE :search)';
            $params['search'] = "%{$search}%";
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE {$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT *
             FROM services
             WHERE {$where}
             ORDER BY is_active DESC, name ASC, Id DESC
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

    public function searchByClinic(int $clinicId, string $search, int $limit = 8): array
    {
        return array_slice($this->paginateCatalogByClinic($clinicId, 1, $limit, $search)['data'] ?? [], 0, $limit);
    }

    public function findCatalogByIdInClinic(int $serviceId, int $clinicId): ?object
    {
        if (!$this->catalogReady()) {
            return null;
        }

        return $this->findByIdInClinic($serviceId, $clinicId);
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
