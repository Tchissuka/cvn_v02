<?php

namespace Source\Models\Reports;

use Source\Core\Model;

class ClinicalReport extends Model
{
    public function __construct()
    {
        parent::__construct('clinical_reports', [], []);
    }
}
