<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Menu extends Model
{
    public function __construct()
    {
        parent::__construct('menus', [], []);
    }
}
