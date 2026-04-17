<?php

namespace Source\Models\Reports;

use Source\Core\Model;

class FinancialReport extends Model
{
    public function __construct()
    {
        parent::__construct('financial_reports', [], []);
    }
}
