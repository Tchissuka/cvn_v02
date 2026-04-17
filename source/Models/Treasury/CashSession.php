<?php

namespace Source\Models\Treasury;

use Source\Core\Model;

class CashSession extends Model
{
    public function __construct()
    {
        parent::__construct('cash_sessions', [], []);
    }
}
