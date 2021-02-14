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
    public static function valid() : bool
    {
        $session = Session::get(name: 'session');

        if (!Session::get(name: 'user')) {
            return false;
        }

        if (time() - Session::get(name: 'last_activity') > Config::get(key: 'auth.session_timeout')) {
            return false;
        }

        if (time() - $session['created_at'] > Config::get(key: 'auth.session_lifetime')) {
            return false;
        }

        if (time() > $session['expires_at']) {
            return false;
        }

        if ($session['ip'] !== Request::ip() && Config::get(key: 'auth.ip_check')) {
            return false;
        }

        Session::set(
            name: 'last_activity',
            data: time()
        );

        return true;
    }
}
