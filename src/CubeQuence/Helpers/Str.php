<?php

namespace CQ\Helpers;

class Str
{
    /**
     * Determines if the given string contains the given value.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        return false !== strpos($haystack, $needle);
    }

    /**
     * Determines if the given string starts with the given value.
     *
     * @param string $haystack
     * @param string $start
     *
     * @return bool
     */
    public static function beginsWith($haystack, $start)
    {
        return substr($haystack, 0, strlen($start)) === $start;
    }

    /**
     * Escape a string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function escape($string)
    {
        $string = trim($string);
        $string = htmlspecialchars($string);

        return stripslashes($string);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @param bool $alphaOnly
     *
     * @return string
     */
    public static function random($length = 32, $alphaOnly = false)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            if ($alphaOnly) {
                $string .= substr(str_replace(['/', '+', '=', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], '', base64_encode($bytes)), 0, $size);
            } else {
                $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
            }
        }

        return $string;
    }

    /**
     * Convert string to binary
     *
     * @param string $string
     *
     * @return string
     */
    public static function toBinary($string)
    {
        $characters = str_split($string);

        $binary = [];

        foreach ($characters as $character) {
            $data = unpack('H*', $character);
            $binary[] = base_convert($data[1], 16, 2);
        }

        return implode(' ', $binary);
    }

    /**
    * Convert binary to string
    *
    * @param string $binary
    *
    * @return string
    */
    public static function binaryToString($binary)
    {
        $binaries = explode(' ', $binary);

        $string = null;

        foreach ($binaries as $binary) {
            $string .= pack('H*', dechex(bindec($binary)));
        }

        return $string;
    }

    /**
     * Insert zero width secret in string
     *
     * @param string $string
     * @param string $secret
     *
     * @return string
     */
    public static function insertZeroWidth($string, $secret)
    {
        if (!ctype_alpha($secret) || strlen($secret) > 8) {
            throw new \Throwable('Secret must be alfabetical and shorter than 9 chars');
        }

        $binary = self::toBinary($secret);
        $characters = str_split($binary);

        $output = array_map(function ($character) {
            if ($character == 1) {
                return "​";
            }

            if ($character == 0) {
                return "‌";
            }

            return "‍";
        }, $characters);

        array_unshift($output, $string);

        return implode("﻿", $output);
    }

    /**
     * Insert zero width secret in string
     *
     * @param string $string
     * @param string $secret
     *
     * @return string
     */
    public static function extractZeroWidth($string)
    {
        $characters = explode("﻿", $string);
        array_shift($characters);

        $output = array_map(function ($character) {
            if ($character == "​") {
                return 1;
            }

            if ($character == "‌") {
                return 0;
            }

            return ' ';
        }, $characters);

        $binary = implode('', $output);

        return self::binaryToString($binary);
    }
}
