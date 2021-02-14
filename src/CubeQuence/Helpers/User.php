<?php

namespace CQ\Helpers;

use CQ\Config\Config;
use CQ\Helpers\Session;

class User
{
    /**
     * Get user id
     *
     * @return string|null
     */
    public static function getId() : string|null
    {
        return Session::get(name: 'user')['id'] ?? null;
    }

    /**
     * Get user name
     *
     * @return string|null
     */
    public static function getName() : string|null
    {
        return Session::get('user')['name'] ?? null;
    }

    /**
     * Get user email
     *
     * @return string|null
     */
    public static function getEmail() : string|null
    {
        return Session::get('user')['email'] ?? null;
    }

    /**
     * Get user roles
     *
     * @return array|null
     */
    public static function getRoles() : array|null
    {
        return Session::get('user')['roles'] ?? null;
    }

    /**
     * Check user role
     *
     * @param string $role
     *
     * @return bool
     */
    public static function hasRole(string $role) : bool
    {
        $roles = self::getRoles();

        return in_array(
            needle: $role,
            haystack: $roles
        );
    }

    /**
     * Check user role
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function valueRole(string $key) : mixed
    {
        $userRole = self::getRoles()[0];

        return Config::get(
            key: "roles.{$userRole}.{$key}",
            fallback: null
        );
    }
}
