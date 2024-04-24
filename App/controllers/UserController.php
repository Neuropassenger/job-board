<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

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

        // Get new user id
        $user_id = $this->db->connection->lastInsertId();

        Session::set('user', [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'city' => $state,
            'state' => $state
        ]);

        redirect('/');
    }

    /**
     * Logout a user and kill session
     * 
     * @return void
     */
    public function logout() {
        Session::clear_all();
        
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

        redirect('/');  
    }

    /**
     * Authenticate a user with email and password
     * 
     * @return void
     */
    public function authenticate() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        //inspect_and_die($_POST);

        $errors = [];

        // Validation
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        // Check for email
        $params = [
            'email' => $email
        ];
        $user = $this->db->query("SELECT * FROM `users` WHERE email = :email", $params)->fetch();
        if (!$user) {
            $errors['email'] = 'Incorrect credentials';
        }

        // Check is password is correct
        if ($user && !password_verify($password, $user->password)) {
            $errors['password'] = 'Incorrect credentials';
        }

        // Check for errors
        if(!empty($errors)) {
            load_view('users/login', [
                'errors' => $errors,
                'email' => $email
            ]);
            exit;
        }

        // OK. Set user session
        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);

        redirect('/');
    }
}