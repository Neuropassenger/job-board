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
 * @return void
 */
function load_view($name) {
    require base_path('views/' . $name . '.php');
}