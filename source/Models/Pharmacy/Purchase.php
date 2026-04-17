<?php

namespace Source\Models\Pharmacy;

use Source\Core\Model;

class Purchase extends Model
{
    public function __construct()
    {
        parent::__construct('purchases', [], []);
    }
}
