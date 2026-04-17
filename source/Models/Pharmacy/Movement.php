<?php

namespace Source\Models\Pharmacy;

use Source\Core\Model;

class Movement extends Model
{
    public function __construct()
    {
        parent::__construct('movements', [], []);
    }
}
