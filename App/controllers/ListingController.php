<?php

namespace App\Controllers;

use Framework\Authorization;
use Framework\Database;
use Framework\Session;
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
        $listings = $this->db->query("SELECT * FROM `listings` ORDER BY created_at DESC")->fetchAll();

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

        $new_listings_data['user_id'] = Session::get('user')['id'];

        // Level 2
        $new_listings_data = array_map('sanitize', $new_listings_data);

        // Validation
        $required_fields = [
            'title',
            'description',
            'email',
            'city',
            'salary'
        ];

        $errors = [];

        foreach ($required_fields as $field_key) {
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

            $field_keys = [];
            $field_values = [];

            foreach ($new_listings_data as $key => $value) {
                if ($value === '') {
                    $value = null;
                } 

                $field_keys[] = $key;
                $field_values[] = ':' . $key;
            }

            $field_keys_str = implode(', ', $field_keys);
            $field_values_str = implode(', ', $field_values);

            $query = "INSERT INTO `listings` ({$field_keys_str}) VALUES ({$field_values_str})";

            inspect($new_listings_data);
            inspect($query);

            $this->db->query($query, $new_listings_data);

            redirect('/listings');
        }
    }

    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params) {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM `listings` WHERE id = :id", $params)->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        // Authorization
        if (!Authorization::is_owner($listing->user_id)) {
            $_SESSION['error_message'] = 'You are not authorize to delete this listing';
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query("DELETE FROM `listings` WHERE id = :id", $params);

        // Set flash message
        $_SESSION['success_message'] = 'Listing deleted successfully';

        redirect('/listings');
    }

    /**
     * Show the listing edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params) {
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

        //inspect_and_die($listing);

        load_view('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array @params
     * @return void
     */
    public function update($params) {
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

        $new_listings_data = [];

        $new_listings_data = array_intersect_key($_POST, array_flip($allowed_fields));

        $new_listings_data = array_map('sanitize', $new_listings_data);

        $required_fields = [
            'title',
            'description',
            'salary',
            'email',
            'city',
            'state'
        ];

        $errors = [];

        foreach ($required_fields as $field_key) {
            if (empty($new_listings_data[$field_key]) || !Validation::string($new_listings_data[$field_key])) {
                $errors[$field_key] = ucfirst($field_key . ' is required');
            }
        }

        if (!empty($errors)) {
            load_view('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        } else {
            // Submit to database
            $update_fields = [];

            foreach(array_keys($new_listings_data) as $key) {
                $update_fields[] = "{$key} = :{$key}";
            }

            $update_fields_str = implode(', ', $update_fields);

            $update_query = "UPDATE `listings` SET {$update_fields_str} WHERE id = :id";

            $new_listings_data['id'] = $id;

            $this->db->query($update_query, $new_listings_data);

            $_SESSION['success_message'] = 'Listing updated';

            redirect('/listings/' . $id);
        }
    }
}