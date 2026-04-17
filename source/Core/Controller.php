<?php

namespace Source\Core;

use Source\Models\Users\Auth;
use Source\Support\Message;
use Source\Support\Seo;

/**
 * FSPHP | Class Controller
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class Controller
{
    /** @var View */
    protected $view;

    /** @var Seo */
    protected $seo;

    /** @var Message */
    protected $message;

    /**
     * Controller constructor.
     * @param string|null $pathToViews
     */
    public function __construct(?string $pathToViews = null)
    {
        $this->view = new View($pathToViews);
        $this->seo = new Seo();
        $this->message = new Message();
    }

    protected function can(string $permission, bool $allowWhenUnassigned = true): bool
    {
        $user = Auth::user();
        return $user ? $user->can($permission, $allowWhenUnassigned) : false;
    }

    protected function canAny(array $permissions, bool $allowWhenUnassigned = true): bool
    {
        $user = Auth::user();
        return $user ? $user->canAny($permissions, $allowWhenUnassigned) : false;
    }

    protected function authorize(string $permission, bool $allowWhenUnassigned = true): void
    {
        if (!$this->can($permission, $allowWhenUnassigned)) {
            redirect('/ops/403');
        }
    }

    protected function authorizeAny(array $permissions, bool $allowWhenUnassigned = true): void
    {
        if (!$this->canAny($permissions, $allowWhenUnassigned)) {
            redirect('/ops/403');
        }
    }
}
