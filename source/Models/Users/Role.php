<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Role extends Model
{
    public function __construct()
    {
        parent::__construct('roles', [], []);
    }
}
