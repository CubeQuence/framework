<?php

namespace CQ\Helpers;

class Auth
{
    /**
     * Check if session active.
     *
     * @return bool
     */
    public static function valid()
    {
        $id_not_empty = Session::get('id');
        $ip_match = Session::get('ip') === Request::ip();
        $session_valid = Session::get('expires') > time();

        return $id_not_empty && $ip_match && $session_valid;
    }
}
