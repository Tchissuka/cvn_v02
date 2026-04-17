<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Users\Auth;

class AppLauncher extends Controller
{
    protected $user;
    protected $data_array = [];
    protected int $clinicId = 0;
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/");

        $this->user = Auth::user();
        if (empty($this->user) &&  !in_array((filter_input(INPUT_GET, "route", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "/"), ['/auth/logout', '/auth/recover'])) {
            redirect("auth/login");
        }
        // Tenta obter o id da clínica a partir do utilizador logado
        $this->clinicId = (int)($this->user->clinic_id ?? $this->user->instit ?? 0);
    }

    /// index
    public function index(): void
    {
        $head = $this->seo->render("CVN-SU - Painel de Controle" . '|' . CONF_SITE_NAME, CONF_SITE_DESC, url(), theme("assets/images/logo.png"), false);
        $person = $this->user->person_full();
        $this->data_array['perfil'] = [
            "name" => $person->full_name ?? $this->user->user_name,
            "email" => $this->user->user_name,
            "photo" => $person->photo ?? theme("assets/images/avatar.png")
        ];
        echo $this->view->render("dashboard/home", ["head" => $head, "data" => $this->data_array]);
    }

    /// contact preciso controlar aqui
    public function contact(array $data): void
    {
        $head = $this->seo->render("CVN-SU - Contato" . '|' . CONF_SITE_NAME, CONF_SITE_DESC, url(), theme("assets/images/logo.png"), false);

        echo $this->view->render("dashboard/contact", ["head" => $head, "data" => $this->data_array]);
    }

    /**
     * SITE NAV ERROR
     * @param array $data
     */
    public function error(array $data): void
    {
        $error = new \stdClass();
        switch ($data['errcode']) {
            case "problemas":
                $error->code = "OPS";
                $error->title = "Estamos enfrentando problemas!";
                $error->message = "Parece que nosso serviço não está diponível no momento. Já estamos vendo isso mas caso precise, envie um e-mail :)";
                $error->linkTitle = "ENVIAR E-MAIL";
                $error->link = "mailto:" . CONF_MAIL_SUPPORT;
                break;

            case "manutencao":
                $error->code = "OPS";
                $error->title = "Desculpe. Estamos em manutenção!";
                $error->message = "Voltamos logo! Por hora estamos trabalhando para melhorar nosso conteúdo para você controlar melhor as suas contas :P";
                $error->linkTitle = null;
                $error->link = null;
                break;

            case "violarRegras":
                $error->code = "Intruso";
                $error->title = "Violou alguma regra!";
                $error->message = "Nossa política de navegação não permite instruções por injecção :)";
                $error->linkTitle = null;
                $error->link = null;
                break;
            default:
                $error->code = $data['errcode'];
                $error->title = ($data['title'] ?? "Ooops. Conteúdo indispinível :/");
                $error->message = ($data['message'] ?? "Sentimos muito, mas o conteúdo que você tentou acessar não existe, está indisponível no momento ou foi removido :/");
                $error->linkTitle = ($data['linkTitle'] ?? "Continue navegando!");
                $error->link = url_back();
                break;
        }

        $head = $this->seo->render(
            "{$error->code} | {$error->title}",
            $error->message,
            url("/ops/{$error->code}"),
            theme("/assets/images/logo.png"),
            false
        );

        echo $this->view->render("error", [
            "head" => $head,
            "error" => $error,
            "route" => $data
        ]);
    }

    protected function renderPlaceholderPage(
        string $routePath,
        string $seoTitle,
        string $seoDescription,
        string $pageTitle,
        string $pageDescription,
        array $highlights = [],
        ?string $badge = 'Em preparação'
    ): void {
        $head = $this->seo->render(
            $seoTitle . ' | ' . CONF_SITE_NAME,
            $seoDescription,
            url($routePath),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/placeholder', [
            'head' => $head,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'highlights' => $highlights,
            'badge' => $badge,
            'routePath' => $routePath
        ]);
    }
}
