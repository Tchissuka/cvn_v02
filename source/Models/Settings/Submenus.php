<?php

namespace Source\Models\Settings;

use Source\Core\Model;

class Submenus extends Model
{
    public function __construct()
    {
        parent::__construct("submenus", ["Id"], ["idPr", "caminho", "nomeSubmenu"]);
    }
    public function submenuNew(string $caminho, string $nomeSubmenu, int $idPr, string $detail, string $metods, int $id = null): bool
    {
        if (empty($caminho) || empty($nomeSubmenu) || empty($idPr) || empty($metods)) {
            $this->message->error("Existem campos obrigatórios não preenchidos...");
            return false;
        }
        if ($this->find("Id!={$id} AND nomeSubmenu='{$nomeSubmenu}'")->count()) {
            $this->message->error("Este menu já existe na base de dados...");
            return false;
        }
        $this->caminho = $caminho;
        $this->nomeSubmenu = $nomeSubmenu;
        $this->idPr = $idPr;
        $this->detail = $detail;
        $this->metods = $metods;
        if ($id) {
            $this->Id = $id;
            $this->save();
            $this->message->success("Submenu alterado com sucesso");
            return true;
        }
        if ($this->save()) {
            $this->message->success("Submenu adicionado a base de dado com sucesso");
            return true;
        }
    }
    public function findByMenp(int $idPr): ?Submenus
    {
        $result = $this->find("idPr=:id", "id={$idPr}")->order("nomeSubmenu");
        return $result->fetch(true);
    }
    public function principal(): ?MainMenu
    {
        if ($this->idPr) {
            return (new MainMenu())->findById($this->idPr);
        }
        return null;
    }
    /**
     * findByName
     *
     * @param  mixed $name
     * @return null|Submenus
     */
    public function findByName(?string $name): ?Submenus
    {
        // Evita consulta desnecessária / aviso de parâmetro vazio
        if (empty($name)) {
            return null;
        }

        $result = $this->find("caminho = :nam", "nam={$name}")->fetch();

        // Garante o tipo de retorno esperedo
        return $result instanceof self ? $result : null;
    }
}
