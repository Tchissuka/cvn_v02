<?php

namespace Source\Models\HR;

use Source\Core\Model;

class Contract extends Model
{
    public function __construct()
    {
        parent::__construct('contracts', [], []);
    }
}
