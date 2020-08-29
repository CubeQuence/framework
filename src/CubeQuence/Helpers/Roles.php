<?php

namespace CQ\Helpers;

use CQ\Config\Config;

class Roles
{
    /**
     * Check if user has role
     *
     * @param string $role
     *
     * @return bool
     */
    public static function has($role)
    {
        return true;
    }

    /**
     * Return values for role
     *
     * @param string $role
     *
     * @return mixed
     */
    public static function info($role)
    {
        return Config::get("roles.{$role}", null);
    }
}
