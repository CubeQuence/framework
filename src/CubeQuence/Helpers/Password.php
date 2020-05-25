<?php

namespace CQ\Helpers;

use CQ\Helpers\Crypt;
use CQ\Helpers\Hash;

class Password
{
    /**
     * encrypt hash from string
     *
     * @param string $string
     * 
     * @return string
     */
    public static function hash($string)
    {
        $hash = Hash::make($string);
        $encryptedHash = Crypt::encrypt($hash);

        return $encryptedHash;
    }

    /**
     * Check plain-text with encrypted hash
     *
     * @param string $checkAgainst
     * @param string $encryptedHash
     * 
     * @return bool
     */
    public static function check($checkAgainst, $encryptedHash)
    {
        $decryptedHash = Crypt::decrypt($encryptedHash);
        $hashValid = Hash::check($checkAgainst, $decryptedHash);

        return $hashValid;
    }
}
