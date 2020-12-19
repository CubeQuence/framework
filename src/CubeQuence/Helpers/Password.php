<?php

namespace CQ\Helpers;

use CQ\Crypto\AES;
use CQ\Crypto\Hash;

class Password
{
    /**
     * encrypt hash from string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function hash($string)
    {
        $hash = Hash::make($string);

        return AES::encrypt($hash);
    }

    /**
     * Check plain-text with encrypted hash.
     *
     * @param string $checkAgainst
     * @param string $encryptedHash
     *
     * @return bool
     */
    public static function check($checkAgainst, $encryptedHash)
    {
        $decryptedHash = AES::decrypt($encryptedHash);

        return Hash::check($checkAgainst, $decryptedHash);
    }
}
