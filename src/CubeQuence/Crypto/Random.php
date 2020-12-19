<?php

namespace CQ\Crypto;

use phpseclib3\Crypt\Random as RandomLib;

class Random
{
    /**
     * Generate random bytes
     *
     * @param string $length
     *
     * @return string
     */
    public static function bytes($length = 32)
    {
        return RandomLib::string($length);
    }

    /**
     * Generate random string
     *
     * @param string $length
     *
     * @return string
     */
    public static function string($length = 32)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = self::bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
