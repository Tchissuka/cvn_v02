<?php

namespace Source\Models\Settings;

use Source\Core\Model;

class MainMenu extends Model
{

    public function __construct()
    {
        parent::__construct("main_menu", ["Id"], ["name_menu", "target_dat"]);
    }


    /**
     * findByNme
     *
     * @param  mixed $name_menu
     * @param  mixed $columns
     * @return MainMenu
     */
    public function findByNme(string $name_menu, string $columns = "Id"): ?MainMenu
    {
        $finding = $this->find("name_menu=:namem", "namem={$name_menu}", $columns);
        return $finding;
    }
    /**
     * Pega sub menus geral 
     * @return Model|mixed|array
     */
    public function submenus()
    {
        $xxz = (new Submenus())->find("idPr={$this->Id}", null, "Id,nomeSubmenu")->order("nomeSubmenu")->fetch(true);
        if ($xxz) {
            return $xxz;
        }
        return null;
    }
    /**
     * Pega sub menus geral 
     * @return Model|mixed|array
     */
    public function submeGrup(int $group)
    {
        $xxz = (new Submenus())->find("idPr={$this->Id}", null, "Id,nomeSubmenu")
            ->order("nomeSubmenu")->fetch(true);
        if ($xxz) {
            return $xxz;
        }
        return null;
    }
}
