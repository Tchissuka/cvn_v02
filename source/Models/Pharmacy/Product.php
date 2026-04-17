<?php

namespace Source\Models\Pharmacy;

use Source\Core\Connect;
use Source\Models\ClinicModel;

class Product extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('products', ['Id'], ['clinic_id', 'name']);
    }

    public function bootstrap(
        int $clinicId,
        string $name,
        ?string $code = null,
        ?string $barcode = null,
        ?string $description = null,
        ?string $unit = null,
        float $costPrice = 0.0,
        float $salePrice = 0.0,
        bool $active = true
    ): self {
        if ($this->columnExists('products', 'clinic_id')) {
            $this->clinic_id = $clinicId;
        }

        $this->{$this->fieldName('name')} = trim($name);
        $this->{$this->fieldName('code')} = $code;
        if ($this->columnExists('products', 'barcode')) {
            $this->barcode = $barcode;
        }
        $this->{$this->fieldName('description')} = $description;
        if ($this->columnExists('products', $this->fieldName('unit'))) {
            $this->{$this->fieldName('unit')} = $unit;
        }
        $this->{$this->fieldName('cost_price')} = $costPrice;
        $this->{$this->fieldName('sale_price')} = $salePrice;
        if ($this->columnExists('products', 'is_active')) {
            $this->is_active = $active ? 1 : 0;
        }
        return $this;
    }

    public function saveProduct(): bool
    {
        $nameColumn = $this->fieldName('name');
        $nameValue = trim((string)($this->{$nameColumn} ?? ''));
        if ($nameValue === '') {
            $this->message->warning("Informe o nome do produto e a clínica.");
            return false;
        }

        if (($this->{$this->fieldName('sale_price')} ?? 0) < 0 || ($this->{$this->fieldName('cost_price')} ?? 0) < 0) {
            $this->message->warning("Preços não podem ser negativos.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o produto, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Produto salvo com sucesso.");
        return true;
    }

    public function catalogReady(): bool
    {
        return $this->tableExists('products');
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

        $nameColumn = $this->fieldName('name');
        $codeColumn = $this->fieldName('code');
        $descriptionColumn = $this->fieldName('description');
        $salePriceColumn = $this->fieldName('sale_price');
        $costPriceColumn = $this->fieldName('cost_price');
        $unitColumn = $this->fieldName('unit');
        $where = '1 = 1';
        $params = [];

        if ($this->columnExists('products', 'clinic_id')) {
            $where .= ' AND clinic_id = :clinic';
            $params['clinic'] = $clinicId;
        }

        if ($search) {
            $where .= " AND ({$nameColumn} LIKE :search OR COALESCE({$codeColumn}, '') LIKE :search OR COALESCE(barcode, '') LIKE :search OR COALESCE({$descriptionColumn}, '') LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE {$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT
                Id,
                {$nameColumn} AS name,
                {$codeColumn} AS code,
                barcode,
                {$descriptionColumn} AS description,
                {$unitColumn} AS unit,
                {$costPriceColumn} AS cost_price,
                {$salePriceColumn} AS sale_price,
                " . ($this->columnExists('products', 'is_active') ? 'is_active' : '1') . " AS is_active,
                created_at,
                updated_at
             FROM products
             WHERE {$where}
             ORDER BY " . ($this->columnExists('products', 'is_active') ? 'is_active DESC,' : '') . " {$nameColumn} ASC, Id DESC
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

    public function findCatalogByIdInClinic(int $productId, int $clinicId): ?object
    {
        if (!$this->catalogReady()) {
            return null;
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return null;
        }

        $nameColumn = $this->fieldName('name');
        $codeColumn = $this->fieldName('code');
        $descriptionColumn = $this->fieldName('description');
        $salePriceColumn = $this->fieldName('sale_price');
        $costPriceColumn = $this->fieldName('cost_price');
        $unitColumn = $this->fieldName('unit');
        $where = 'Id = :product';
        $params = ['product' => $productId];

        if ($this->columnExists('products', 'clinic_id')) {
            $where .= ' AND clinic_id = :clinic';
            $params['clinic'] = $clinicId;
        }

        $stmt = $pdo->prepare(
            "SELECT
                Id,
                {$nameColumn} AS name,
                {$codeColumn} AS code,
                barcode,
                {$descriptionColumn} AS description,
                {$unitColumn} AS unit,
                {$costPriceColumn} AS cost_price,
                {$salePriceColumn} AS sale_price,
                " . ($this->columnExists('products', 'is_active') ? 'is_active' : '1') . " AS is_active,
                created_at,
                updated_at
             FROM products
             WHERE {$where}
             LIMIT 1"
        );
        $stmt->execute($params);

        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    private function fieldName(string $logicalField): string
    {
        return match ($logicalField) {
            'name' => $this->columnExists('products', 'name') ? 'name' : 'name_product',
            'code' => $this->columnExists('products', 'code') ? 'code' : 'internal_code',
            'description' => $this->columnExists('products', 'description') ? 'description' : 'detal_product',
            'sale_price' => $this->columnExists('products', 'sale_price') ? 'sale_price' : 'price',
            'cost_price' => $this->columnExists('products', 'cost_price') ? 'cost_price' : 'cust_price',
            'unit' => $this->columnExists('products', 'unit') ? 'unit' : 'type_measure',
            default => $logicalField,
        };
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
