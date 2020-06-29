<?php

namespace CQ\Helpers;

class Hash
{
    /**
     * Hash string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function make($string)
    {
        $hash_cost = 2; // Can be increased in the future
        $hash_algorithm = PASSWORD_ARGON2I;
        $hash_options = [
            'memory_cost' => $hash_cost * PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => $hash_cost * PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => $hash_cost * PASSWORD_ARGON2_DEFAULT_THREADS,
        ];

        return password_hash(
            $string,
            $hash_algorithm,
            $hash_options
        );
    }

    /**
     * Check plain-text with hash.
     *
     * @param string $checkAgainst
     * @param string $hash
     *
     * @return bool
     */
    public static function check($checkAgainst, $hash)
    {
        return password_verify($checkAgainst, $hash);
    }
}
