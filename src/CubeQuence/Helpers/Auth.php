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

        if (!Session::get('user')) {
            return false;
        }

        if (time() - Session::get('last_activity') > Config::get('auth.session_timeout')) {
            return false;
        }

        if (time() - $session['created_at'] > Config::get('auth.session_lifetime')) {
            return false;
        }

        if (time() > $session['expires_at']) {
            return false;
        }

        if ($session['ip'] !== Request::ip() && Config::get('auth.ip_check')) {
            return false;
        }

        Session::set('last_activity', time());

        return true;
    }
}
