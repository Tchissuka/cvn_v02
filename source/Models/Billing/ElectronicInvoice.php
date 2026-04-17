<?php

namespace Source\Models\Billing;

use Source\Core\Model;

class ElectronicInvoice extends Model
{
    public function __construct()
    {
        parent::__construct('electronic_invoices', [], []);
    }
}
