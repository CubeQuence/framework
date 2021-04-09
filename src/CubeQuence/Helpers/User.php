<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Config\Config;

// TODO: maybe just use CQ\OAuth\Models\User

final class User
{
    /**
     * Get user id
     */
    public static function getId(): string | null
    {
        return Session::get(name: 'user')['id'] ?? null;
    }

    /**
     * Get user email
     */
    public static function getEmail(): string | null
    {
        return Session::get('user')['email'] ?? null;
    }

    /**
     * Get user roles
     *
     * @return array|null
     */
    public static function getRoles(): array | null
    {
        return Session::get('user')['roles'] ?? null;
    }

    /**
     * Check user role
     */
    public static function hasRole(string $role): bool
    {
        $roles = self::getRoles();

        return in_array(
            needle: $role,
            haystack: $roles
        );
    }

    /**
     * Check user role
     */
    // TODO: stupid name, what does this function do?
    public static function valueRole(string $key): mixed
    {
        $userRole = self::getRoles()[0];

        return Config::get(
            key: "roles.{$userRole}.{$key}",
            fallback: null
        );
    }
}
