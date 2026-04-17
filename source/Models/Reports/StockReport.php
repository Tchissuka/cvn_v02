<?php

namespace Source\Models\Reports;

use Source\Core\Model;

class StockReport extends Model
{
    public function __construct()
    {
        parent::__construct('stock_reports', [], []);
    }
}
