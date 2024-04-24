<?php

namespace Framework;

use Framework\Session;

class Authorization {
    /**
     * Check if current logged user owns a resource
     * 
     * @param int $resource_user_id
     * 
     * @return bool
     */
    public static function is_owner($resource_user_id) {
        $session_user = Session::get('user');
        if ($session_user !== null && isset($session_user['id'])) {
            $session_user_id = (int) $session_user['id'];
            return $session_user_id === $resource_user_id;
        }

        return false;
    }
}