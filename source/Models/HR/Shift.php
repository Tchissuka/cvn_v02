<?php

namespace Source\Models\HR;

use Source\Core\Model;

class Shift extends Model
{
    public function __construct()
    {
        parent::__construct('shifts', [], []);
    }
}
