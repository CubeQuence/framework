<?php

namespace CQ\Helpers;

class Str
{
    /**
     * Escape a string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function escape(string $string) : string
    {
        $string = trim(str: $string);
        $string = stripslashes(str: $string);
        $string = htmlspecialchars(string: $string);

        return $string;
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @param bool $alpha_only
     *
     * @return string
     */
    public static function random(int $length = 32, bool $alpha_only = false) : string
    {
        $string = '';

        while (($len = strlen(string: $string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes(length: $size);

            if ($alpha_only) {
                $string .= substr(
                    string: str_replace(
                        search: ['/', '+', '=', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                        replace: '',
                        subject: base64_encode($bytes)
                    ),
                    start: 0,
                    length: $size
                );
            } else {
                $string .= substr(
                    str_replace(
                        search: ['/', '+', '='],
                        replace: '',
                        subject: base64_encode($bytes)
                    ),
                    0,
                    $size
                );
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
    public static function toBinary(string $string) : string
    {
        $characters = str_split(string: $string);

        $binary = [];

        foreach ($characters as $character) {
            $data = unpack(
                format: 'H*',
                data: $character
            );

            $binary[] = base_convert(
                number: $data[1],
                frombase: 16,
                tobase: 2
            );
        }

        return implode(
            glue: ' ',
            pieces: $binary
        );
    }

    /**
    * Convert binary to string
    *
    * @param string $binary
    *
    * @return string
    */
    public static function binaryToString(string $binary) : string
    {
        $binaries = explode(
            delimiter:' ',
            string: $binary
        );

        $string = null;

        foreach ($binaries as $binary) {
            $string .= pack(
                format: 'H*',
                args: dechex( // TODO: check if args is valid named argument
                    number: bindec(binary_string: $binary)
                )
            );
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
     * @throws \Exception
     */
    public static function insertZeroWidth(string $string, string $secret) : string
    {
        if (!ctype_alpha(text: $secret) || strlen(string: $secret) > 8) {
            throw new \Exception(message: 'Secret must be alfabetical and shorter than 9 chars');
        }

        $binary = self::toBinary(string: $secret);
        $characters = str_split(string: $binary);

        $output = array_map(
            callback: function ($character) {
                if ($character == 1) {
                    return "​";
                }

                if ($character == 0) {
                    return "‌";
                }

                return "‍";
            },
            arr1: $characters
        );

        array_unshift(
            array: $output,
            vars: $string // TODO: check if vars is valid named argument
        );

        return implode(
            glue: "﻿",
            pieces: $output
        );
    }

    /**
     * Extract zero width secret in string
     *
     * @param string $string
     *
     * @return string
     */
    public static function extractZeroWidth(string $string) : string
    {
        $characters = explode(delimiter: "﻿", string: $string);
        array_shift(array: $characters);

        $output = array_map(
            callback: function ($character) {
                if ($character == "​") {
                    return 1;
                }

                if ($character == "‌") {
                    return 0;
                }

                return ' ';
            },
            arr1: $characters
        );

        $binary = implode(
            glue: '',
            pieces: $output
        );

        return self::binaryToString(binary: $binary);
    }
}
