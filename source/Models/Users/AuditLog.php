<?php

namespace Source\Models\Users;

use Source\Core\Model;

class AuditLog extends Model
{
    public function __construct()
    {
        parent::__construct('audit_logs', ['Id'], ['clinic_id', 'entity', 'entity_id', 'action']);
    }

    public function bootstrap(
        int $clinicId,
        string $entity,
        int $entityId,
        string $action,
        ?int $userId = null,
        ?string $changes = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->entity = $entity;
        $this->entity_id = $entityId;
        $this->action = $action;
        $this->user_id = $userId;
        $this->changes = $changes;
        $this->ip_address = $ip;
        $this->user_agent = $userAgent;
        return $this;
    }

    public function register(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Dados obrigatórios em falta para registar auditoria.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao registar auditoria.");
            }
            return false;
        }

        return true;
    }
}
