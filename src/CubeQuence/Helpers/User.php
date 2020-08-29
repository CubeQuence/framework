<?php

namespace CQ\Helpers;

use CQ\Config\Config;
use CQ\Helpers\Session;

class User
{
    /**
     * Get user id
     *
     * @return string
     */
    public static function getId()
    {
        return Session::get('user')['id'];
    }

    /**
     * Get user name
     *
     * @return string
     */
    public static function getName()
    {
        return Session::get('user')['name'];
    }

    /**
     * Get user email
     *
     * @return string
     */
    public static function getEmail()
    {
        return Session::get('user')['email'];
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public static function getRoles()
    {
        return Session::get('user')['roles'];
    }

    /**
     * Check user role
     *
     * @param string $role
     *
     * @return bool
     */
    public static function hasRole($role)
    {
        $roles = self::getRoles();

        return in_array($role, $roles);
    }

    /**
     * Check user role
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function valueRole($key)
    {
        $userRole = User::getRoles()[0];

        return Config::get("roles.{$userRole}.{$key}", null);
    }
}
