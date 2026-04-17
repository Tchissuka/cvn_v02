<?php

namespace Source\Models\Pharmacy;

use Source\Core\Model;

class Stock extends Model
{
    public function __construct()
    {
        parent::__construct('stock', [], []);
    }
}
