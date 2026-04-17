<?php

namespace Source\Models\HR;

use Source\Core\Model;

class Attendance extends Model
{
    public function __construct()
    {
        parent::__construct('attendances', [], []);
    }
}
