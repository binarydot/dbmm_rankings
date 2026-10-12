<?php

namespace App\Libraries;

use Config\Database;

abstract class LegacyModel
{
    protected $db;

    public function __construct()
    {
        $this->db = new LegacyDatabase(Database::connect());
    }
}
