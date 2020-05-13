<?php

namespace CQ\Helpers;

use CQ\Helpers\Session;

class Auth
{
    /**
     * Check if session active
     * 
     * @return bool
     */
    public static function valid()
    {
        $id_not_empty = Session::get('id');
        $ip_match = Session::get('ip') === $_SERVER['REMOTE_ADDR'];
        $session_valid = Session::get('expires') > time();

        // TODO: maybe revalidate jwt

        return $id_not_empty && $ip_match && $session_valid;
    }
}
