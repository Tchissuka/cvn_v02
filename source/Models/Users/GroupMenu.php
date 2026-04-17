<?php

namespace Source\Models\Users;

use Source\Core\Model;

class GroupMenu extends Model
{
    /**
     * GroupMenu constructor.
     */
    public function __construct()
    {
        parent::__construct("group_menus", ['Id'], ["id_grup", "id_subMen"]);
    }

    public function addSubmenGroup(int $idGrup, int $idSubmenu, int $idUser): bool
    {
        // Verificar se o registro já existe para evitar duplicidade
        $existing = $this->find("id_grup = :idGrup AND id_subMen = :idSubmenu", "idGrup={$idGrup}&idSubmenu={$idSubmenu}")->fetch();
        if ($existing) {
            return true; // Registro já existe, considerar como sucesso
        }
        $this->id_grup = $idGrup;
        $this->id_subMen = $idSubmenu;
        $this->created_by = $idUser;
        return $this->save();
    }
}
