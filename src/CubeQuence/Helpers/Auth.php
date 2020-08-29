<?php

namespace CQ\Helpers;

use CQ\Config\Config;

class Auth
{
    /**
     * Check if session active.
     *
     * @return bool
     */
    public static function valid()
    {
        $session = Session::get('session');

        if (Config::get('auth.session_timeout') > time() - Session::get('last_activity')) {
            return false;
        }

        if (Config::get('auth.session_lifetime') > time() - $session['created_at']) {
            return false;
        }

        if ($session['expires_at'] > time()) {
            return false;
        }

        if ($session['ip'] !== Request::ip() && Config::get('auth.ip_check')) {
            return false;
        }

        Session::set('last_activity', time());

        return true;
    }
}
