<?php

namespace Source\Models\Treasury;

use Source\Core\Model;

class BankTransaction extends Model
{
    public function __construct()
    {
        parent::__construct('bank_transactions', [], []);
    }
}
