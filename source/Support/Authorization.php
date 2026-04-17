<?php

namespace Source\Support;

use Source\Core\Connect;
use Source\Models\Users\Auth;
use Source\Models\Users\User;

class Authorization
{
    private ?User $user;
    private ?int $normalizedUserId = null;
    private ?array $roleSlugs = null;
    private ?array $permissionSlugs = null;
    private ?bool $schemaAvailable = null;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? Auth::user();
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function roles(): array
    {
        if ($this->roleSlugs !== null) {
            return $this->roleSlugs;
        }

        $userId = $this->resolveNormalizedUserId();
        if (!$userId) {
            return $this->roleSlugs = [];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return $this->roleSlugs = [];
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT r.slug
                 FROM role_user ru
                 INNER JOIN roles r ON r.id = ru.role_id
                 WHERE ru.user_id = :user_id"
            );
            $stmt->execute(["user_id" => $userId]);
        } catch (\PDOException $exception) {
            return $this->roleSlugs = [];
        }

        return $this->roleSlugs = array_values(array_unique(array_filter($stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [])));
    }

    public function permissions(): array
    {
        if ($this->permissionSlugs !== null) {
            return $this->permissionSlugs;
        }

        $userId = $this->resolveNormalizedUserId();
        if (!$userId) {
            return $this->permissionSlugs = [];
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return $this->permissionSlugs = [];
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT DISTINCT p.slug
                 FROM role_user ru
                 INNER JOIN permission_role pr ON pr.role_id = ru.role_id
                 INNER JOIN permissions p ON p.id = pr.permission_id
                 WHERE ru.user_id = :user_id"
            );
            $stmt->execute(["user_id" => $userId]);
        } catch (\PDOException $exception) {
            return $this->permissionSlugs = [];
        }

        return $this->permissionSlugs = array_values(array_unique(array_filter($stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [])));
    }

    public function hasRole(string $roleSlug, bool $allowWhenUnassigned = true): bool
    {
        if (!$this->user || $roleSlug === '') {
            return false;
        }

        $roles = $this->roles();
        if (empty($roles)) {
            return $allowWhenUnassigned;
        }

        return in_array($roleSlug, $roles, true);
    }

    public function can(string $permissionSlug, bool $allowWhenUnassigned = true): bool
    {
        if ($permissionSlug === '') {
            return true;
        }

        if (!$this->user) {
            return false;
        }

        $permissions = $this->permissions();
        if (empty($permissions)) {
            return $allowWhenUnassigned;
        }

        return in_array($permissionSlug, $permissions, true);
    }

    public function canAny(array $permissionSlugs, bool $allowWhenUnassigned = true): bool
    {
        $permissionSlugs = array_values(array_filter(array_map('strval', $permissionSlugs)));
        if (empty($permissionSlugs)) {
            return true;
        }

        if (!$this->user) {
            return false;
        }

        $permissions = $this->permissions();
        if (empty($permissions)) {
            return $allowWhenUnassigned;
        }

        foreach ($permissionSlugs as $permissionSlug) {
            if (in_array($permissionSlug, $permissions, true)) {
                return true;
            }
        }

        return false;
    }

    public function isRbacAssigned(): bool
    {
        return !empty($this->roles()) || !empty($this->permissions());
    }

    private function resolveNormalizedUserId(): ?int
    {
        if ($this->normalizedUserId !== null) {
            return $this->normalizedUserId;
        }

        if (!$this->user) {
            return $this->normalizedUserId = 0;
        }

        if (!$this->isSchemaAvailable()) {
            return $this->normalizedUserId = 0;
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return $this->normalizedUserId = 0;
        }

        $candidateId = (int)($this->user->id ?? $this->user->Id ?? 0);
        if ($candidateId > 0) {
            return $this->normalizedUserId = $candidateId;
        }

        $candidateEmail = trim((string)($this->user->email ?? $this->user->user_name ?? ''));
        if ($candidateEmail !== '') {
            try {
                $stmt = $pdo->prepare("SELECT Id FROM users WHERE user_name = :email LIMIT 1");
                $stmt->execute(["email" => $candidateEmail]);
                $resolvedId = (int)($stmt->fetchColumn() ?: 0);
                if ($resolvedId > 0) {
                    return $this->normalizedUserId = $resolvedId;
                }
            } catch (\PDOException $exception) {
                return $this->normalizedUserId = 0;
            }
        }

        return $this->normalizedUserId = 0;
    }

    private function isSchemaAvailable(): bool
    {
        if ($this->schemaAvailable !== null) {
            return $this->schemaAvailable;
        }

        $pdo = Connect::getInstance();
        if (!$pdo instanceof \PDO) {
            return $this->schemaAvailable = false;
        }

        try {
                 $requiredTables = ['roles', 'permissions', 'role_user', 'permission_role'];
            $stmt = $pdo->query(
                "SELECT table_name
                 FROM information_schema.tables
                 WHERE table_schema = DATABASE()
                     AND table_name IN ('roles', 'permissions', 'role_user', 'permission_role')"
            );
            $tables = array_map('strval', $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []);

            foreach ($requiredTables as $tableName) {
                if (!in_array($tableName, $tables, true)) {
                    return $this->schemaAvailable = false;
                }
            }

            return $this->schemaAvailable = true;
        } catch (\PDOException $exception) {
            return $this->schemaAvailable = false;
        }
    }
}