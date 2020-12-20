<?php

namespace CQ\Crypto;

class Hash
{
    private static $hash_cost = 2;

    /**
     * Hash string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function make($string)
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            return password_hash($string, PASSWORD_BCRYPT);
        }

        $hash_options = [
            'memory_cost' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_THREADS,
        ];

        return password_hash(
            $string,
            PASSWORD_ARGON2ID,
            $hash_options
        );
    }

    /**
     * Verify plain-text with hash.
     *
     * @param string $checkAgainst
     * @param string $hash
     *
     * @return bool
     */
    public static function verify($checkAgainst, $hash)
    {
        return password_verify($checkAgainst, $hash);
    }
}
