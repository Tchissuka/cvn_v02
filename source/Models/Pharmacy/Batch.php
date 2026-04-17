<?php

namespace Source\Models\Pharmacy;

use Source\Core\Model;

class Batch extends Model
{
    public function __construct()
    {
        parent::__construct('batches', [], []);
    }
}
