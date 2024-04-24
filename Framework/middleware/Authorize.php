<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize {
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function is_authenticated() {
        return Session::has('user');
    }

    /**
     * Handle the user's request
     * 
     * @param string $role
     * @return bool
     */
    public function handle($role) {
        if ($role === 'guest' && $this->is_authenticated()) {
            return redirect('/');
        } else if ($role === 'auth' && !$this->is_authenticated()) {
            return redirect('/auth/login');
        }
    }
}