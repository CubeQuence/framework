<?php

namespace CQ\Helpers;

use CQ\Config\Config;
use Exception;

class Crypt
{
    /**
     * Encrypt string.
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

        return base64_encode($encNonce.$encString);
    }

    /**
     * Decrypt string.
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

        return sodium_crypto_secretbox_open($encString, $encNonce, $encKey);
    }

    /**
     * Get enc key from config.
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
}
