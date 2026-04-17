<?php

namespace Source\Models\Users;

use Source\Core\Model;
use Source\Core\Session;
use Source\Core\View;
use Source\Models\Faq\CommunicateGroup;
use Source\Models\Settings\Institution;
use Source\Models\Settings\Notification;
use Source\Models\Settings\Submenus;
use Source\Models\Settings\Themes;
use Source\Support\Email;

/**
 * Class Auth
 * @package Source\Models
 */
class Auth extends Model
{
    /** @var int */
    private $sessionTime;

    /**
     * Auth constructor.
     */
    public function __construct(int  $sessionTime = 45)
    {
        $this->sessionTime = $sessionTime;
        parent::__construct("users", ["Id"], ["user_name", "passwd"]);
    }

    /**
     * @return null|User
     */
    public static function user(): ?User
    {
        $session = new Session();
        if (!$session->has("authUser")) {
            return null;
        }
        return (new User())->findById($session->authUser);
    }

    /**
     * log-out
     */
    public static function logout(): void
    {
        $session = new Session();
        if ($session->all()) {
            foreach ($session->all() as $key => $value) {
                $session->unset($key);
            }
        }
    }

    /**
     * @param string $user_name
     * @param string $password
     * @param bool $save
     * @return bool
     */
    public function login(string $user_name, string $password): bool
    {

        if (!is_passwd($password)) {
            $this->message->warning("A senha informada não é válida");
            return false;
        }

        $user = (new User())->find("user_name=:u", "u={$user_name}")->fetch();
        if (!$user) {
            $this->message->error("O utilizador informado não está cadastrado");
            return false;
        }
        $user->passwd = (md5($password) == $user->passwd ? passwd($password) : $user->passwd);
        if (!passwd_verify($password, $user->passwd)) {
            $this->message->error("A senha informada não confere");
            return false;
        }

        if ($password = passwd_rehash($user->passwd)) {
            $user->passwd = $password;
        }

        $user->ultimoAcesso = $user->ultimoAcesso + 1;
        $user->horaOn = date('H:i:s');
        $user->onOff = 'Y';
        $user->dataUltimoAcesso = date_fmt_app(date('Y-m-d H:i:s'));
        $user->agent = $_SERVER['HTTP_USER_AGENT'];
        $user->ipLogado = $_SERVER['REMOTE_ADDR'];
        $user->save();
        //LOGIN
        $setar = (new Session())->set("authUser", $user->Id);
        $setar->set("user_name", $user->user_name);
        $setar->set("ultimoAcesso", $user->ultimoAcesso);
        $setar->set("tipoUtili", $user->tipoUtili);
        $setar->set("instit", $user->instit);
        /** VERIFICAR SE PODE VER FICHA RECURSOS HUMANOS */
        $user->ID_SESSAO = $setar->__get('csrf_token');
        $eed = $user->person_full("full_name,genre,photo");
        $setar->set("nomeCon", $eed->full_name);
        $setar->set("sexo", $eed->genre);
        $setar->set("photo", $eed->photo);

        $user->save();
        $this->message->success("Seja bem-vindo, verifique suas notificações")->flashJson();
        return true;
    }


    /**
     * @param string $user_name
     * @return bool
     */
    public function forget(string $user_name): bool
    {
        $adress = (new PersonalAddress())->findByEmail($user_name, "personal_id");
        if (!$adress) {
            $this->message->warning("E-mail informado não está cadastrado em nossa base de dados.");
            return false;
        }

        $user = (new User())->findById($adress->personal_id);
        if (!$user) {
            $this->message->warning("Deculpa mas ainda não é usuário do SGA, por favor contactar os administradores do sistema..");
            return false;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();
        $full = $user->person_full("full_name,genre");
        $view = new View(__DIR__ . "/../../../shared/views/email");
        $message = $view->render("forget", [
            "first_name" => $full->full_name,
            "empresa" => (new Institution())->findById($user->instit)->instituicaoName,
            "forget_link" => url("/recuperar/{$user->user_name}|{$user->forget}")
        ]);
        (new Email())->bootstrap(
            "Recupere sua senha no " . CONF_SITE_NAME,
            $message,
            $user_name,
            "{$full->full_name}"
        )->queue();
        return true;
    }

    /**
     * @param string $user_name
     * @param string $code
     * @param string $password
     * @param string $passwordRe
     * @return bool
     */
    public function reset(string $user_name, string $code, string $password, string $passwordRe): bool
    {
        $user = (new User())->findUserName($user_name);

        if (!$user) {
            $this->message->warning("A conta para recuperação não foi encontrada.");
            return false;
        }
        if ($user->forget != $code) {
            $this->message->error("Desculpe, mas o código de verificação não é válido.");
            return false;
        }
        if (!is_passwd($password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->info("Sua senha deve ter entre {$min} e {$max} caracteres.");
            return false;
        }

        if ($password != $passwordRe) {
            $this->message->warning("Você informou duas senhas diferentes.");
            return false;
        }

        $user->passwd = $password;
        $user->forget = null;
        $user->save();
        return true;
    }


    /**
     * @param bool $count
     * @return array|int|null
     */
    public function findByActive(string $type = 'A', bool $count = false)
    {
        $type2 = ($type == 'A' ? 'E' : ($type == 'E' ? 'A' : 'T'));
        $find = $this->find(
            "estatutoUtiliza!='{$type2}' AND onOff='Y' AND instit>0 AND updated_at >= NOW() - INTERVAL {$this->sessionTime} MINUTE",
            null,
            "Id,ultimoAcesso,tipoUtili,horaOn,instit,lastPage,agent,updated_at,ipLogado,(SELECT instituicaoName FROM institutions WHERE Id=instit) AS instituicaoName"
        );
        if ($count) {
            return $find->count();
        }

        $find->order("updated_at DESC");
        return $find->fetch(true);
    }
    /**
     * Dados pessoais
     * @return null|Personal
     */
    public function full_name(): ?Personal
    {
        return (new Personal())->findById($this->Id);
    }

    /**
     * birthdayPerson
     *
     * @param  mixed $count
     * @return array|int|null
     */
    public function birthdayPerson(bool $count = false)
    {
        $traba = (new Employees())->find("id_person IN (SELECT Id FROM personal WHERE Id=id_person AND  DATE_FORMAT(birth_date,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d'))", null, "id_person,(SELECT full_name FROM personal WHERE Id=id_person ) AS nomes");
        if ($count) {
            return $traba->count();
        }
        return $traba->fetch(true);
    }

    /**
     * communicated by user
     *
     * @param  mixed $count
     * @return array|int|null
     */
    public function communicated(bool $count = false)
    {

        if ($dd = $this->user()) {
            $gruCom = (new CommunicateGroup())->find("grupo_id={$dd->tipoUtili} AND school_id_group={$dd->instit}
            AND NOT EXISTS (SELECT id_comunica FROM communicated_user WHERE id_comunica=comunica_id AND id_person={$dd->Id})");
            $bbvc = $gruCom->count();
            $noti = (new Notification())->find("person_id={$dd->Id} AND wien_at IS NULL");
            $bbvc += $noti->count();
            if ($count) {
                return $bbvc;
            }
            return ($gruCom->order("created_at DESC")->limit(10)->fetch(true) ??  $noti->order("created_at DESC")->limit(10)->fetch(true));
        }
        return null;
    }
    /**
     *Meu endereço real
     * @return null|PersonalAddress
     */
    public function myAdress(): PersonalAddress
    {
        return (new PersonalAddress())->find("personal_id={$this->Id}")->fetch();
    }
    /**
     * Tipo de útilizador
     * @return null|UserType
     */
    public function typeUser(): ?UserType
    {
        return (new UserType())->findById($this->tipoUtili, 'nomeTipoUtilizador');
    }

    /**
     * Mostra dados da escola
     * @return null|Institution
     */
    public function school(): Institution
    {

        return (new Institution())->findById("Id={$this->instit}", "instituicaoName");
    }

    public function finishReset(User $logar): bool
    {
        $logar->ultimoAcesso = $logar->ultimoAcesso + 1;
        $logar->horaOn = date('H:i:s');
        $logar->onOff = 'Y';
        $logar->dataUltimoAcesso = date_fmt_app(date('Y-m-d H:i:s'));
        $logar->agent = $_SERVER['HTTP_USER_AGENT'];
        $logar->ipLogado = $_SERVER['REMOTE_ADDR'];
        $logar->email_verified_at = date_fmt_app(date('Y-m-d H:i:s'));
        $logar->save();
        //LOGIN
        $inst = (new Institution())->findById($logar->instit);
        $setar = (new Session())->set("authUser", $logar->Id);
        $setar->set("user_name", $logar->user_name);
        $setar->set("ultimoAcesso", $logar->ultimoAcesso);
        $setar->set("tipoUtili", $logar->tipoUtili);
        $setar->set("instit", $logar->instit);
        $setar->set("style", $inst->style);
        /** VERIFICAR SE PODE VER FICHA RECURSOS HUMANOS */
        if ($bv = (new UseSubmenu())->find("idPerson={$logar->Id} AND idSubmen=11", null, "Id")->fetch()) {
            $setar->set("seach", $bv);
        }
        $setar->set("instN", $inst->instituicaoName);
        $setar->set("startCopy", $inst->startCopy);
        $setar->set("LogoMark", $inst->LogoMark);
        $logar->ID_SESSAO = $setar->__get('csrf_token');
        $eed = $logar->person_full("full_name,genre");
        $setar->set("nomeCon", $eed->full_name);
        $setar->set("sexo", $eed->genre);
        $logar->save();
        $mytheme = (new Themes)->myTheme($logar->Id);
        if (!$mytheme) {
            $dsc = new Themes();
            $dsc->person_id = $logar->Id;
            $dsc->main_head = 'gradient-red';
            $dsc->sidebar = 'gradient-red-nav';
            if ($dsc->save()) {
                $mytheme = $dsc;
            }
        }
        $setar->set("main_head", $mytheme->main_head);
        $setar->set("sidebar", $mytheme->sidebar);
        $this->message->success("Terminar o processo de recuperação da senha")->flashJson();
        return true;
    }

    /**
     * MinMenu
     *
     * @param  mixed $url
     * @return null|Submenus
     */
    public function MinMenu(string $url): ?Submenus
    {
        return (new Submenus())->find("caminho='{$url}' AND Id IN (SELECT idSubmen FROM user_submenu WHERE idPerson={$this->Id})", null, "Id,
        caminho,
        nomeSubmenu,
        idPr,
        detail,
        metods");
    }

    /**
     * MinMenuArray
     *
     * @param  mixed $idPr
     * @return null|Submenus
     */
    public function MinMenuArray(int $idPr): ?Submenus
    {
        return (new Submenus())->find("idPr='{$idPr}' AND Id IN (SELECT idSubmen FROM user_submenu WHERE idPerson={$this->Id})", null, "Id,
        caminho,
        nomeSubmenu,
        idPr,
        detail,
        metods");
    }
}
