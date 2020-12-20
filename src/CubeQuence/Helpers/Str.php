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
     *
     * @return string
     */
    public static function random($length = 32)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
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
        $binary = self::toBinary($secret);
        $characters = explode('', $binary);

        $output = array_map(function ($character) {
            if ($character === 1) {
                return '​';
            }

            if ($character === 0) {
                return '‌';
            }

            return '‍';
        }, $characters);

        return $string . implode('﻿', $output);
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
        $clean_string = str_replace("&#8203;", '', $string);
        $clean_string = str_replace("&#8204;", '', $clean_string);
        $clean_string = str_replace("&#8205;", '', $clean_string);
        $clean_string = str_replace("&#65279;", '', $clean_string);

        $input = str_replace($clean_string, '', $string);

        $output = array_map(function ($character) {
            if ($character === '​') {
                return 1;
            }

            if ($character === '‌') {
                return 0;
            }

            return ' ';
        }, $input);

        $binary = implode('', $output);

        return self::binaryToString($binary);
    }
}
