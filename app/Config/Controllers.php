<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

class TestDB extends Controller
{
    public function index()
    {
        $db = Database::connect();
        $tables = $db->listTables();
        echo '<pre>';
        print_r($tables);
    }
}
