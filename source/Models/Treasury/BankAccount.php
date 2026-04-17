<?php

namespace Source\Models\Treasury;

use Source\Core\Model;

class BankAccount extends Model
{
    public function __construct()
    {
        parent::__construct('bank_accounts', [], []);
    }
}
