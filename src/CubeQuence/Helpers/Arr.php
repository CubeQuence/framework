<?php

namespace CQ\Helpers;

use ArrayAccess;

class Arr
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible($value) : bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param ArrayAccess|array $array
     * @param string|int        $key
     *
     * @return bool
     */
    public static function exists(
        ArrayAccess|array $array,
        string|int $key
    ) : bool {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists(offset: $key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param ArrayAccess|array $array
     * @param string|int|null   $key
     * @param mixed             $default
     *
     * @return mixed
     */
    public static function get(
        ArrayAccess|array $array,
        string|int|null $key,
        $default = null
    ) : mixed {
        if (!self::accessible(value: $array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (self::exists(
            array: $array,
            key: $key
        )) {
            return $array[$key];
        }

        if (false === strpos(haystack: $key, needle: '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (self::accessible(value: $array) && self::exists(array: $array, key: $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
