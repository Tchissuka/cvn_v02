<?php

namespace Source\App;

use Source\Models\Users\Menu;
use Source\Models\Users\MenuGroup;
use Source\Models\Settings\Numerador;
use Source\Models\Users\Clinic;

class Setting extends AppLauncher
{

    public function __construct()
    {
        parent::__construct();
        $this->authorizeAny(['menus.manage', 'roles.manage', 'institution.manage']);
    }

    /**
     * Página inicial do módulo de configurações
     */
    public function index(): void
    {
        $head = $this->seo->render(
            "Configurações do Sistema | " . CONF_SITE_NAME,
            "Gestão de menus, permissões, numeração e dados institucionais",
            url("/setting"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/index", [
            "head" => $head
        ]);
    }

    /**
     * Gestão de menus principais
     */
    public function menus(): void
    {
        $this->authorize('menus.manage');

        // POST: criação/edição via AJAX
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleMenuSave();
            return;
        }

        $menuModel = new Menu();

        // Menus principais = menus sem parent_id
        $menus = $menuModel->find("parent_id IS NULL", null, "*")
            ->order("sort_order ASC, name ASC")
            ->fetch(true) ?? [];

        // Mapeia campos para os nomes usados na view
        foreach ($menus as $menu) {
            $menu->base_route = $menu->route;
            $menu->menu_order = $menu->sort_order;
            $menu->status = $menu->is_active;
        }

        $head = $this->seo->render(
            "Menus Principais | " . CONF_SITE_NAME,
            "Configuração dos menus principais do sistema",
            url("/setting/menus"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/menus", [
            "head" => $head,
            "menus" => $menus,
            "pagination" => null
        ]);
    }

    /**
     * Gestão de submenus (rotas/controller@action)
     */
    public function submenus(): void
    {
        $this->authorize('menus.manage');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleSubmenuSave();
            return;
        }

        $menuModel = new Menu();

        // Menus principais
        $menus = $menuModel->find("parent_id IS NULL", null, "*")
            ->order("sort_order ASC, name ASC")
            ->fetch(true) ?? [];

        // Submenus com join simples para obter o nome do menu principal
        $query = "SELECT m.Id, m.name, m.route, m.sort_order, m.is_active, p.name AS main_menu_name
                  FROM menus m
                  LEFT JOIN menus p ON p.id = m.parent_id
                  WHERE m.parent_id IS NOT NULL
                  ORDER BY p.name ASC, m.sort_order ASC, m.name ASC";

        $submenuModel = new Menu();
        $submenus = $submenuModel->join($query) ?? [];

        foreach ($submenus as $submenu) {
            $submenu->submenu_order = $submenu->sort_order;
            $submenu->status = $submenu->is_active;
        }

        $head = $this->seo->render(
            "Submenus | " . CONF_SITE_NAME,
            "Configuração de submenus e rotas do sistema",
            url("/setting/submenus"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/submenus", [
            "head" => $head,
            "submenus" => $submenus,
            "menus" => $menus,
            "pagination" => null
        ]);
    }

    /**
     * Gestão de grupos/perfis de utilizador
     */
    public function groups(): void
    {
        $this->authorize('roles.manage');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleGroupSave();
            return;
        }

        $groupModel = new MenuGroup();
        $groups = $groupModel->find()->order("name ASC")->fetch(true) ?? [];

        foreach ($groups as $group) {
            $group->status = $group->is_default;
        }

        $head = $this->seo->render(
            "Grupos de Utilizador | " . CONF_SITE_NAME,
            "Configuração de grupos/perfis e permissões básicas",
            url("/setting/groups"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/groups", [
            "head" => $head,
            "groups" => $groups,
            "pagination" => null
        ]);
    }

    /**
     * Gestão de numeração geral (tabela general_numerator)
     */
    public function numerador(): void
    {
        $this->authorize('institution.manage');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleNumeradorSave();
            return;
        }

        $model = new Numerador();
        $numerators = $model->find("clinic_id = :cId", "cId={$this->clinicId}", "*")
            ->order("type_num_id ASC")
            ->fetch(true) ?? [];

        $head = $this->seo->render(
            "Numeração | " . CONF_SITE_NAME,
            "Configuração de tipos e modos de numeração",
            url("/setting/numerador"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/numerador", [
            "head" => $head,
            "numerators" => $numerators,
            "pagination" => null
        ]);
    }

    /**
     * Dados institucionais da clínica/instalação
     */
    public function institution(): void
    {
        $this->authorize('institution.manage');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleInstitutionSave();
            return;
        }

        $clinic = null;
        if ($this->clinicId) {
            $clinic = (new Clinic())->findById($this->clinicId);
        }

        $head = $this->seo->render(
            "Dados Institucionais | " . CONF_SITE_NAME,
            "Configuração de dados institucionais da clínica/empresa",
            url("/setting/institution"),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/setting/institution", [
            "head" => $head,
            "clinicId" => $this->clinicId,
            "clinic" => $clinic
        ]);
    }

    // ========================
    // Handlers AJAX (CRUD básico)
    // ========================

    private function respondJson(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
    }

    private function handleMenuSave(): void
    {
        if (!csrf_verify($_POST)) {
            $this->respondJson([
                "success" => false,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $icon = trim(filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $baseRoute = trim(filter_input(INPUT_POST, 'base_route', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $order = filter_input(INPUT_POST, 'menu_order', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);

        if ($name === '' || $icon === '') {
            $this->respondJson([
                "success" => false,
                "message" => "Nome e ícone do menu são obrigatórios.",
                "errors" => ["name", "icon"]
            ]);
            return;
        }

        $menuModel = new Menu();
        $menu = $id ? $menuModel->findById($id) : new Menu();

        if ($id && !$menu) {
            $this->respondJson([
                "success" => false,
                "message" => "Menu não encontrado para edição."
            ]);
            return;
        }

        $menu->name = $name;
        $menu->slug = str_slug($name);
        $menu->icon = $icon;
        $menu->route = $baseRoute !== '' ? $baseRoute : null;
        $menu->parent_id = null; // sempre menu principal
        $menu->sort_order = $order ?? 0;
        $menu->is_active = $status === 0 ? 0 : 1;

        if (!$menu->save()) {
            $this->respondJson([
                "success" => false,
                "message" => $menu->message()->getText() ?: "Falha ao guardar menu."
            ]);
            return;
        }

        $this->respondJson([
            "success" => true,
            "message" => "Menu guardado com sucesso.",
            "id" => (int)$menu->Id
        ]);
    }

    private function handleSubmenuSave(): void
    {
        if (!csrf_verify($_POST)) {
            $this->respondJson([
                "success" => false,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $mainMenuId = filter_input(INPUT_POST, 'main_menu_id', FILTER_VALIDATE_INT) ?: null;
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $route = trim(filter_input(INPUT_POST, 'route', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $order = filter_input(INPUT_POST, 'submenu_order', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);

        if (!$mainMenuId || $name === '' || $route === '') {
            $this->respondJson([
                "success" => false,
                "message" => "Menu principal, nome e rota são obrigatórios.",
                "errors" => ["main_menu_id", "name", "route"]
            ]);
            return;
        }

        $menuModel = new Menu();
        $parent = $menuModel->findById($mainMenuId);
        if (!$parent) {
            $this->respondJson([
                "success" => false,
                "message" => "Menu principal não encontrado."
            ]);
            return;
        }

        $submenu = $id ? $menuModel->findById($id) : new Menu();
        if ($id && !$submenu) {
            $this->respondJson([
                "success" => false,
                "message" => "Submenu não encontrado para edição."
            ]);
            return;
        }

        $submenu->name = $name;
        $submenu->slug = str_slug($parent->slug . '-' . $name);
        $submenu->icon = $parent->icon; // herda ícone do menu principal ou defina outro campo se necessário
        $submenu->route = $route;
        $submenu->parent_id = $mainMenuId;
        $submenu->sort_order = $order ?? 0;
        $submenu->is_active = $status === 0 ? 0 : 1;

        if (!$submenu->save()) {
            $this->respondJson([
                "success" => false,
                "message" => $submenu->message()->getText() ?: "Falha ao guardar submenu."
            ]);
            return;
        }

        $this->respondJson([
            "success" => true,
            "message" => "Submenu guardado com sucesso.",
            "id" => (int)$submenu->Id
        ]);
    }

    private function handleGroupSave(): void
    {
        if (!csrf_verify($_POST)) {
            $this->respondJson([
                "success" => false,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);

        if ($name === '') {
            $this->respondJson([
                "success" => false,
                "message" => "Nome do grupo é obrigatório.",
                "errors" => ["name"]
            ]);
            return;
        }

        $groupModel = new MenuGroup();
        $group = $id ? $groupModel->findById($id) : new MenuGroup();

        if ($id && !$group) {
            $this->respondJson([
                "success" => false,
                "message" => "Grupo não encontrado para edição."
            ]);
            return;
        }

        $group->name = $name;
        $group->slug = str_slug($name);
        $group->description = $description !== '' ? $description : null;
        $group->is_default = $status === 1 ? 1 : 0;

        if (!$group->save()) {
            $this->respondJson([
                "success" => false,
                "message" => $group->message()->getText() ?: "Falha ao guardar grupo."
            ]);
            return;
        }

        $this->respondJson([
            "success" => true,
            "message" => "Grupo guardado com sucesso.",
            "id" => (int)$group->Id
        ]);
    }

    private function handleNumeradorSave(): void
    {
        if (!csrf_verify($_POST)) {
            $this->respondJson([
                "success" => false,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $type = filter_input(INPUT_POST, 'type_num_id', FILTER_VALIDATE_INT);
        $mode = filter_input(INPUT_POST, 'mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'A';
        $year = filter_input(INPUT_POST, 'number_year', FILTER_VALIDATE_INT) ?: (int)date('Y');
        $number = filter_input(INPUT_POST, 'number', FILTER_VALIDATE_INT) ?: 1;

        if (!$type) {
            $this->respondJson([
                "success" => false,
                "message" => "Código/tipo de numeração é obrigatório.",
                "errors" => ["type_num_id"]
            ]);
            return;
        }

        $model = new Numerador();
        $numerador = $id
            ? $model->find("Id = :id AND clinic_id = :cId", "id={$id}&cId={$this->clinicId}")->fetch()
            : new Numerador();

        if ($id && !$numerador) {
            $this->respondJson([
                "success" => false,
                "message" => "Registo de numeração não encontrado para edição."
            ]);
            return;
        }

        $numerador->clinic_id = $this->clinicId;
        $numerador->type_num_id = $type;
        $numerador->mode = in_array($mode, ['A', 'C'], true) ? $mode : 'A';
        $numerador->number_year = $year;
        $numerador->number = $number;

        if (!$numerador->save()) {
            $this->respondJson([
                "success" => false,
                "message" => $numerador->message()->getText() ?: "Falha ao guardar configuração de numeração."
            ]);
            return;
        }

        $this->respondJson([
            "success" => true,
            "message" => "Configuração de numeração guardada com sucesso.",
            "id" => (int)$numerador->Id
        ]);
    }

    private function handleInstitutionSave(): void
    {
        if (!csrf_verify($_POST)) {
            $this->respondJson([
                "success" => false,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $nif = trim(filter_input(INPUT_POST, 'nif', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $clinicCode = trim(filter_input(INPUT_POST, 'clinic_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');

        if ($name === '') {
            $this->respondJson([
                "success" => false,
                "message" => "Nome da clínica/empresa é obrigatório.",
                "errors" => ["name"]
            ]);
            return;
        }

        $clinicModel = new Clinic();
        $clinic = $this->clinicId ? $clinicModel->findById($this->clinicId) : null;
        if (!$clinic) {
            $clinic = new Clinic();
        }

        $clinic->name = $name;
        $clinic->code = $clinicCode !== '' ? $clinicCode : null;
        $clinic->tax_id = $nif !== '' ? $nif : null;
        $clinic->address = $address !== '' ? $address : null;
        $clinic->phone = $phone !== '' ? $phone : null;
        $clinic->email = $email !== '' ? $email : null;

        if (!$clinic->save()) {
            $this->respondJson([
                "success" => false,
                "message" => $clinic->message()->getText() ?: "Falha ao guardar dados institucionais."
            ]);
            return;
        }

        $this->respondJson([
            "success" => true,
            "message" => "Dados institucionais guardados com sucesso.",
            "id" => (int)$clinic->Id
        ]);
    }
}
