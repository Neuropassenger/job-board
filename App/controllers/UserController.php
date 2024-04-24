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

    /**
     * Store user in database
     * 
     * @return void
     */
    public function store() {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];

        $errors = [];

        // Validation
        if(!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if(!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        if(!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if(!Validation::match($password, $password_confirmation)) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        // Check if email exists
        $params = [
            'email' => $email,
        ];

        $user = $this->db->query("SELECT * FROM `users` WHERE email = :email", $params)->fetch();

        //inspect_and_die($user);

        if ($user) {
            $errors['email'] = 'Email already exists';
        }

        if (!empty($errors)) {
            load_view('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state,
                ]
            ]);
            exit;
        }

        // Create user account
        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $this->db->query("INSERT INTO `users` (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password);", $params);
        redirect('/');
    }
}