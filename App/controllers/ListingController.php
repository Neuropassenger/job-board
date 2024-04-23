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
        // First level of security
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

        // Second level of security
        $new_listings_data = array_map('sanitize', $new_listings_data);

        inspect_and_die($new_listings_data);
    }
}