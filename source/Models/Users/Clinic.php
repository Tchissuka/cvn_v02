<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Clinic extends Model
{
    public function __construct()
    {
        parent::__construct('clinics', [], []);
    }
}
