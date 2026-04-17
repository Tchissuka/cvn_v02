<?php

namespace Source\Models\Clinical;

use Source\Core\Model;

class Prescription extends Model
{
    public function __construct()
    {
        parent::__construct('prescriptions', [], []);
    }
}
