<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Base para modelos operacionais com clinic_id.
 * Fornece helpers de escopo por clínica e paginação simples.
 */
abstract class ClinicModel extends Model
{
    /** @var string */
    protected string $clinicField = "clinic_id";

    /** @var string */
    protected string $idField = "Id";

    /**
     * Define a clínica atual neste registo.
     */
    public function setClinic(int $clinicId): static
    {
        $this->{$this->clinicField} = $clinicId;
        return $this;
    }

    /**
     * Obtém um registo pela PK garantindo que pertence à clínica.
     */
    public function findByIdInClinic(int $id, int $clinicId, string $columns = "*")
    {
        $this->find("{$this->idField} = :id AND {$this->clinicField} = :clinic", "id={$id}&clinic={$clinicId}", $columns);
        return $this->fetch();
    }

    /**
     * Lista paginada por clínica, opcionalmente com termos adicionais.
     */
    public function paginateByClinic(
        int $clinicId,
        int $page = 1,
        int $perPage = 20,
        ?string $terms = null,
        ?string $params = null,
        string $columns = "*",
        ?string $order = null
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $order = $order ?: "{$this->idField} DESC";

        $baseTerms = "{$this->clinicField} = :clinic";
        $baseParams = "clinic={$clinicId}";

        if ($terms) {
            $terms = $baseTerms . " AND (" . $terms . ")";
            $params = $baseParams . (empty($params) ? "" : "&" . $params);
        } else {
            $terms = $baseTerms;
            $params = $baseParams;
        }

        $this->find($terms, $params, $columns);
        $total = $this->count($this->idField);

        $pages = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $this->order($order)->limit($perPage)->offset($offset);

        return [
            "data" => $this->fetch(true) ?? [],
            "total" => $total,
            "page" => $page,
            "pages" => $pages,
            "per_page" => $perPage
        ];
    }
}
