<?php

namespace Source\Models\Users;

use Source\Core\Model;
use Source\Models\Settings\Submenus;
use Source\Support\Authorization;

/**
 * FSPHP | Class User Active Record Pattern
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Models
 */
class User extends Model
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["dataUltimoAcesso"], ["Id", "instit", "passwd", "tipoUtili", "user_name"]);
    }
    /**
     * @param int $tipoUtili
     * @param string $user_name
     * @param int $instit
     * @param string $passwd
     * @return User
     */
    public function bootstrap(int $tipoUtili, string $user_name, array $data): User
    {
        $this->tipoUtili = $tipoUtili;
        $this->user_name = $user_name;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }


    /**
     * findUserName
     *
     * @param  mixed $name
     * @return User
     */
    public function findUserName(string $name, string $colum = "*"): ?User
    {
        $find = $this->find("user_name =:use", "use={$name}", $colum)->fetch();
        return $find;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Utilizador, senha e grupo campos obrigatórios...");
            return false;
        }

        if (!is_passwd($this->passwd)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("A senha deve ter entre {$min} e {$max} caracteres");
            return false;
        } else {
            $this->passwd = passwd($this->passwd);
        }

        /** User Update */
        if ($this->findById($this->Id)) {
            if ($this->find("user_name =:u AND Id !=:i", "u={$this->user_name}&i={$this->Id}", "Id")->fetch()) {
                $this->message->warning("O nome de utilizador informado já está cadastrado");
                return false;
            }

            $this->update($this->safe(), "Id = :id", "id={$this->Id}");
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
            $this->message->success("Utilizador actualizado com sucesso");
            return true;
        }

        /** User Create */
        if (empty($this->findById($this->Id))) {
            if ($this->findUserName($this->user_name, "Id")) {
                $this->message->warning("O nome de utilizador informado já está cadastrado");
                return false;
            }
            $userId = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Erro ao cadastrar, verifique os dados");
                return false;
            }
            $this->message->success("Registo de utilizador feito com sucesso...");
        }
        $this->data = ($this->findById($userId))->data();
        /// carregar menus para usuario registado      
        $grupmenu = (new GroupMenu())->find("id_grup={$this->tipoUtili}")->fetch(true);
        if ($grupmenu) {
            foreach ($grupmenu as $yc) {
                (new Usermenu())->addSubmenUser($this->Id, $yc->id_subMen, $this->Id);
            }
        }
        return true;
    }
    /**
     * person_full
     *
     * @return null|Personal
     */
    public function person_full(string $colunn = "*"): ?Personal
    {
        if ($this->Id) {
            return (new Personal())->findById($this->Id, $colunn);
        }
        return null;
    }

    /**
     * adressUser
     *
     * @param  mixed $colunn
    * @return null|object
     */
    public function adressUser(string $colunn = "*"): ?object
    {
        $personalAddressClass = __NAMESPACE__ . "\\PersonalAddress";
        if (!class_exists($personalAddressClass)) {
            return null;
        }

        return (new $personalAddressClass())->find("personal_id={$this->Id}", null, $colunn)->fetch();
    }

    /**
     * Lista funcionarios instituição *
     * @return null|array|Model
     */
    public function institionUser(int $instit)
    {
        $find = $this->find("instit={$instit} AND  estatutoUtiliza!='E'")
            ->order("user_name")
            ->fetch(true);
        return $find;
    }

    /**
     * Mostra grupo de utilizadores
     *
     * @return null| UserType
     */
    public function grouUser(): ?object
    {
        $userTypeClass = __NAMESPACE__ . "\\UserType";
        if (!class_exists($userTypeClass)) {
            return null;
        }

        return (new $userTypeClass())->find("Id={$this->tipoUtili}", null, "Id,name")->fetch();
    }

    /**
     * Funcionario dados
    * @return null|Employee
     */
    public function employee(): ?Employee
    {
        return (new Employee())->find("id_person={$this->Id}")->fetch();
    }
    /**
     * array de submenus pessoais
     *
     * @return null|array
     */
    public function perfilMenu(): ?array
    {
        return (new Usermenu())->find("idPerson={$this->Id}", null, "idSubmen")->fetch(true);
    }
    /**
     * person_search
     *
     * @return null|Personal
     */
    public function person_search(): ?Personal
    {
        return (new Personal())->findById($this->Id);
    }

    public function submenus(): Submenus
    {
        return (new Submenus())->find()->order("nomeSubmenu")->fetch(true);
    }

    /**
     * areaSale
     *
     * @return null|array
     */
    public function areaSale(): ?array
    {
        $areaUserClass = 'Source\\Models\\Settings\\Areauser';
        if (!class_exists($areaUserClass)) {
            return null;
        }

        return (new $areaUserClass())->find("user_id={$this->Id}")->fetch(true);
    }

    public function roles(): array
    {
        return (new Authorization($this))->roles();
    }

    public function permissions(): array
    {
        return (new Authorization($this))->permissions();
    }

    public function hasRole(string $roleSlug, bool $allowWhenUnassigned = true): bool
    {
        return (new Authorization($this))->hasRole($roleSlug, $allowWhenUnassigned);
    }

    public function can(string $permissionSlug, bool $allowWhenUnassigned = true): bool
    {
        return (new Authorization($this))->can($permissionSlug, $allowWhenUnassigned);
    }

    public function canAny(array $permissionSlugs, bool $allowWhenUnassigned = true): bool
    {
        return (new Authorization($this))->canAny($permissionSlugs, $allowWhenUnassigned);
    }

    /**
     * photo
     *
     * @return string
     */
    public function photo(): string
    {
        if ($nnv = (new Personal())->findById($this->Id, "genre,
        photo")) {
            if ($nnv->photo) {
                return image($nnv->photo, 145, 54, $nnv->genre);
            }
            return image(null, 145, 54, $nnv->genre);
        }

        return image(null, 145, 54, null);
    }
}
