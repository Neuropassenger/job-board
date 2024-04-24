<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class UserController {
    protected $db;

    public function __construct() {
        $config = require base_path('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the Login Page
     * 
     * @return void
     */
    public function login() {
        load_view('users/login');
    }

    /**
     * Show the Register Page
     * 
     * @return void
     */
    public function create() {
        load_view('users/create');
    }
}