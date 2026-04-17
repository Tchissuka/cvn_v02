<?php

namespace Source\Models\Clinical;

use Source\Core\Model;

class Consultation extends Model
{
    public function __construct()
    {
        parent::__construct('consultations', [], []);
    }
}
