<?php

namespace App\Controllers;

use Framework\Database;

class ListingController {
    protected $db;

    public function __construct() {
        $config = require base_path('config/db.php');
        $this->db = new Database($config);
    }

    public function index() {
        $listings = $this->db->query("SELECT * FROM `listings` LIMIT 6")->fetchAll();

        load_view('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create() {
        load_view('listings/create');
    }

    public function show() {
        $id = $_GET['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query("SELECT * FROM `listings` WHERE id = :id", $params)->fetch();

        load_view('listings/show', [
            'listing' => $listing
        ]);
    }
}