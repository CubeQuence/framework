<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Config\Config;

final class AuthHelper // TODO: move authlogic to seperate folder
{
    /**
     * Check if session active.
     */
    public static function valid(): bool
    {
        $session = SessionHelper::get(name: 'session');

        if (! SessionHelper::get(name: 'user')) {
            return false;
        }

        if (time() - SessionHelper::get(name: 'last_activity') > Config::get(key: 'auth.session_timeout')) {
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

        SessionHelper::set(
            name: 'last_activity',
            data: time()
        );

        return true;
    }
}
