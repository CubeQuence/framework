<?php

declare(strict_types=1);

namespace CQ\Helpers;

class Str
{
    /**
     * Escape a string.
     */
    public static function escape(string $string): string
    {
        $string = trim(str: $string);
        $string = stripslashes(str: $string);

        return htmlspecialchars(string: $string);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     */
    public static function random(int $length = 32, bool $alpha_only = false): string
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
}
