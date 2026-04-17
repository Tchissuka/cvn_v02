<?php

namespace Source\Models\Reports;

use Source\Core\Model;

class HRReport extends Model
{
    public function __construct()
    {
        parent::__construct('hr_reports', [], []);
    }
}
