<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Users\Auth;

class Authentic extends Controller
{
    protected $logiUser;
    /**
     * Authentic constructor.
     * @return void
     */
    public function __construct()
    {
        $this->logiUser = Auth::user();
        parent::__construct(__DIR__ . "/../../themes/");
        if (empty($this->logiUser) &&  !in_array((filter_input(INPUT_GET, "route", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "/"), ['/auth/login', '/auth/recover'])) {
            redirect("auth/login");
        } elseif (!empty($this->logiUser) && in_array((filter_input(INPUT_GET, "route", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "/"), ['/auth/login', '/auth/recover'])) {
            redirect("/");
        }
    }


    /// login
    public function login(array $data): void
    {
        // Se houver dados, tratamos como tentativa de login (POST via router)
        if (!empty($data)) {
            header('Content-Type: application/json; charset=utf-8');

            // Proteção CSRF
            if (!csrf_verify($data)) {
                echo json_encode([
                    "error" => true,
                    "message" => "Requisição inválida. Atualize a página e tente novamente."
                ]);
                return;
            }

            // Limite de tentativas por chave (IP + usuário) para evitar brute force
            $loginId = trim($data['email'] ?? '');
            $rateKey = 'auth_login_' . ($loginId ?: ($_SERVER['REMOTE_ADDR'] ?? 'guest'));
            if (request_limit($rateKey, 5, 300)) { // 5 tentativas em 5 minutos
                echo json_encode([
                    "error" => true,
                    "message" => "Muitas tentativas de login. Aguarde alguns minutos e tente novamente."
                ]);
                return;
            }

            $userName = filter_var($loginId, FILTER_SANITIZE_EMAIL) ?: $loginId;
            $password = (string)($data['password'] ?? '');

            if ($userName === '' || $password === '') {
                echo json_encode([
                    "error" => true,
                    "message" => "Informe o utilizador e a senha."
                ]);
                return;
            }

            $auth = new Auth();
            $loggedIn = $auth->login($userName, $password);
            $msg = $auth->message()->getText() ?: ($loggedIn ? 'Login realizado com sucesso!' : 'Email ou senha inválidos.');

            if ($loggedIn) {
                // Login bem-sucedido: limpa a contagem de tentativas dessa chave de rate limit
                if (function_exists('request_limit_clear')) {
                    request_limit_clear($rateKey);
                }

                echo json_encode([
                    "redirect" => url("/"),
                    "message" => $msg,
                    "success" => true
                ]);
            } else {
                echo json_encode([
                    "error" => true,
                    "message" => $msg
                ]);
            }
            return;
        }

        $head = $this->seo->render("HandekaSMS - Login" . '|' . CONF_SITE_NAME, CONF_SITE_DESC, url(), theme("assets/images/logo.png"), false);
        echo $this->view->render("authentication/login", ["head" => $head]);
    }

    /// logout
    public function logout(array $data): void
    {
        $auth = new Auth();
        $auth->logout();

        $head = $this->seo->render("HandekaSMS - Logout" . '|' . CONF_SITE_NAME, CONF_SITE_DESC, url(), theme("assets/images/logo.png"), false);
        echo $this->view->render("authentication/logout", ["head" => $head]);
    }

    /// recover 
    public function recover(array $data): void
    {
        $head = $this->seo->render("HandekaSMS - Recuperar Senha" . '|' . CONF_SITE_NAME, CONF_SITE_DESC, url(), theme("assets/images/logo.png"), false);
        echo $this->view->render("authentication/recover", ["head" => $head]);
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
}
