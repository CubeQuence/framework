<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Config\Config;

class AppHelper
{
    public static function getEnvoironment()
    {
        return Config::get(
            key: 'app.env',
            fallback: 'production'
        );
    }

    public static function isEnvironment(string $check)
    {
        return self::getEnvoironment() === $check;
    }

    /**
     * Return if debug is enabled.
     */
    public static function isDebug(): bool
    {
        // Can't debug in production
        if (self::isEnvironment('production')) {
            return false;
        }

        return Config::get(
            key: 'app.debug',
            fallback: false
        );
    }

    /**
     * Get project root string
     */
    public static function getRootPath(): string
    {
        [$path] = get_included_files();

        $path = dirname(path: $path);

        return str_replace(
            search: '/public',
            replace: '',
            subject: $path
        );
    }
}
