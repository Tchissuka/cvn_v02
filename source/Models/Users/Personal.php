<?php

namespace Source\Models\Users;

use Source\Core\Model;

class Personal extends Model
{
    /**
     * Personal constructor.
     */
    public function __construct()
    {
        parent::__construct("personal", ['Id'], ["full_name"]);
    }
}
