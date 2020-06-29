<?php

namespace CQ\Helpers;

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

        return Crypt::encrypt($hash);
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
        $decryptedHash = Crypt::decrypt($encryptedHash);

        return Hash::check($checkAgainst, $decryptedHash);
    }
}
