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
    public static function make(string $string) : string
    {
        if (!defined(name: 'PASSWORD_ARGON2ID')) {
            return password_hash(
                password: $string,
                algo: PASSWORD_BCRYPT
            );
        }

        $hash_options = [
            'memory_cost' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => self::$hash_cost * PASSWORD_ARGON2_DEFAULT_THREADS,
        ];

        return password_hash(
            password: $string,
            algo: PASSWORD_ARGON2ID,
            options: $hash_options
        );
    }

    /**
     * Verify plain-text with hash.
     *
     * @param string $check_against
     * @param string $hash
     *
     * @return bool
     */
    public static function verify(string $check_against, string $hash) : bool
    {
        return password_verify(
            password: $check_against,
            hash: $hash
        );
    }
}
