<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController {
    protected $db;

    /**
     * A constructor for the ListingController class
     */
    public function __construct() {
        $config = require base_path('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Load a view for the Lsitings Page
     *
     * @return void
     */
    public function index() {
        $listings = $this->db->query("SELECT * FROM `listings` LIMIT 6")->fetchAll();

        load_view('listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Load a view for the Create Listing Page
     *
     * @return void
     */
    public function create() {
        load_view('listings/create');
    }

    /**
     * Load a view for the Single Listing Page
     *
     * @param array $params
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query("SELECT * FROM `listings` WHERE id = :id", $params)->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        load_view('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * 
     * @return void
     */
    public function store() {
        // Sanitizing
        
        // Level 1
        $allowed_fields = [
            'title',
            'description',
            'salary',
            'tags',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
            'requirements',
            'benefits'
        ];

        $new_listings_data = array_intersect_key($_POST, array_flip($allowed_fields));

        $new_listings_data['user_id'] = 1;

        // Level 2
        $new_listings_data = array_map('sanitize', $new_listings_data);

        // Validation
        $required_fields = [
            'title',
            'description',
            'email',
            'city'
        ];

        $errors = [];

        foreach($required_fields as $field_key) {
            if (empty($new_listings_data[$field_key]) || !Validation::string($new_listings_data[$field_key])) {
                $errors[$field_key] = ucfirst($field_key) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Reload view with errors
            load_view('listings/create', [
                'errors' => $errors,
                'listing' => $new_listings_data
            ]);
        } else {
            // Submit data
            echo "Success";
        }
    }
}