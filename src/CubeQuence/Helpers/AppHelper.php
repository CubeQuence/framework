<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Config\Config;
use CQ\Crypto\Models\SymmetricKey;
use CQ\Crypto\Symmetric;

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

    /**
     * Encrypt with appKey
     */
    public static function encrypt(string $string) : string
    {
        $appKey = new SymmetricKey(
            encodedKey: Config::get('app.key')
        );

        $symmetric = new Symmetric(
            key: $appKey
        );

        return $symmetric->encrypt(string: $string);
    }

    /**
     * Decrypt with appKey
     */
    public static function decrypt(string $encryptedString) : string
    {
        $appKey = new SymmetricKey(
            encodedKey: Config::get('app.key')
        );

        $symmetric = new Symmetric(
            key: $appKey
        );

        return $symmetric->decrypt(encryptedString: $encryptedString);
    }
}
