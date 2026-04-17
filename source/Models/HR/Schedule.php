<?php

namespace Source\Models\HR;

use Source\Core\Model;

class Schedule extends Model
{
    public function __construct()
    {
        parent::__construct('schedules', [], []);
    }
}
