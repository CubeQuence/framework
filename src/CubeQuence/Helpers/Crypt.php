<?php


namespace CQ\Helpers;

use Exception;
use CQ\Helpers\Str;
use CQ\Config\Config;

class Crypt
{
    /**
     * Get enc key from config
     *
     * @return string
     */
    private static function getEncryptionKey()
    {
        $encKey = Config::get('app.key');

        if (!$encKey) {
            throw new Exception('No encryption key provided');
        }

        return $encKey;
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * 
     * @return string
     */
    public static function encrypt($string)
    {
        $encKey = self::getEncryptionKey();
        $encNonce = Str::random(24);

        $encString = sodium_crypto_secretbox($string, $encNonce, $encKey);

        $encryptedString = base64_encode($encNonce . $encString);

        return $encryptedString;
    }

    /**
     * Decrypt string
     *
     * @param string $encryptedString
     * 
     * @return string
     */
    public static function decrypt($encryptedString)
    {
        $encKey = self::getEncryptionKey();
        $decodedEncString = base64_decode($encryptedString);

        $encNonce = mb_substr($decodedEncString, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $encString = mb_substr($decodedEncString, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $string = sodium_crypto_secretbox_open($encString, $encNonce, $encKey);

        return $string;
    }
}
