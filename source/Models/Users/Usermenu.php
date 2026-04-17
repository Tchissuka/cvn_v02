<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Usermenu extends Model
{
    /**
     * Usermenu constructor.
     */
    public function __construct()
    {
        parent::__construct("user_submenu", ['Id'], ["idPerson", "idSubmenu"]);
    }

    public function addSubmenUser(int $idPerson, int $idSubmenu, int $idUser): bool
    {
        // Verificar se o registro já existe para evitar duplicidade
        $existing = $this->find("idPerson = :idPerson AND idSubmenu = :idSubmenu", "idPerson={$idPerson}&idSubmenu={$idSubmenu}")->fetch();
        if ($existing) {
            return true; // Registro já existe, considerar como sucesso
        }
        $this->idPerson = $idPerson;
        $this->idSubmenu = $idSubmenu;
        $this->created_by = $idUser;
        return $this->save();
    }
}
