<?php

namespace Source\Models\Users;

use Source\Core\Model;

class MenuGroup extends Model
{
    public function __construct()
    {
        parent::__construct('menu_groups', [], ['name', 'slug']);
    }
}
