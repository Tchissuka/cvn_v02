<?php

namespace Source\Models\Clinical;

use Source\Core\Model;

class Exam extends Model
{
    public function __construct()
    {
        parent::__construct('exams', [], []);
    }
}
