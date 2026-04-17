<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Employee extends Model
{
    /**
     * Employee constructor.
     */
    public function __construct()
    {
        parent::__construct("employees", ['Id'], ["id_person"]);
    }
}
