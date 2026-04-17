<?php

namespace Source\Models\Clinical;

use Source\Core\Model;

class Record extends Model
{
    public function __construct()
    {
        parent::__construct('records', [], []);
    }
}
