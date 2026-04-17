<?php

namespace Source\Models\Pharmacy;

use Source\Core\Model;

class Sale extends Model
{
    public function __construct()
    {
        parent::__construct('sales', [], []);
    }
}
