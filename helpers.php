<?php

/**
 * Get the base path
 * 
 * @param string @path
 * @return string
 */
function base_path($path = '') {
    return __DIR__ . '/' . $path;   
}

/** 
 * Load a view
 * 
 * @param string $name
 * @param array $data
 * @return void
 */
function load_view($name, $data) {
    $view_path = base_path('views/' . $name . '.php');

    if (file_exists($view_path)) {
        extract($data);
        require $view_path;
    } else {
        echo "View '{$name}' not found!";
    }
}

/** 
 * Load a partial
 * 
 * @param string $name
 * @return void
 */
function load_partial($name) {
    $patial_path = base_path('views/partials/' . $name . '.php');
    
    if (file_exists($patial_path)) {
        require $patial_path;
    } else {
        echo "Partial '{$name}' not found!";
    }
}

/**
 * Inspect a value(s)
 * 
 * @param mixed $value
 * @return void
 */
function inspect($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/**
 * Inspect value(s) and die
 * 
 * @param mixed $value
 * @return void
 */
function inspect_and_die($value) {
    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
}

/**
 * Format salary 
 * 
 * @param string $value
 * @return string formatted salary
 */
function format_salary($value) {
    return '$ ' . number_format($value);
}