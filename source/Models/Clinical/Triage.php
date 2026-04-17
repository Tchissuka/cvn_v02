<?php

namespace Source\Models\Clinical;

use Source\Core\Model;

class Triage extends Model
{
    public function __construct()
    {
        parent::__construct('triages', [], []);
    }
}
