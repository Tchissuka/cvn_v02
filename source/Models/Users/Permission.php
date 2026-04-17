<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Permission extends Model
{
    public function __construct()
    {
        parent::__construct('permissions', [], []);
    }
}
