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
        if (!function_exists('sodium_crypto_secretbox')) {
            return self::legacy_encrypt($string);
        }

        $encKey = self::getEncryptionKey();
        $encNonce = Str::random(24);

        $encString = sodium_crypto_secretbox($string, $encNonce, $encKey);

        return base64_encode($encNonce.$encString);
    }

    /**
     * Encrypt string if sodium is not supported.
     *
     * @param string $string
     *
     * @return string
     */
    private static function legacy_encrypt($string)
    {
        $encMethod = 'AES-256-CBC';
        $encKey = self::getEncryptionKey();

        $ivLength = openssl_cipher_iv_length($encMethod);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedString = openssl_encrypt($string, $encMethod, $encKey, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encryptedString) . '|' . base64_encode($iv);
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
        if (!function_exists('sodium_crypto_secretbox_open')) {
            return self::legacy_decrypt($encryptedString);
        }

        $encKey = self::getEncryptionKey();
        $decodedEncString = base64_decode($encryptedString);

        $encNonce = mb_substr($decodedEncString, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $encString = mb_substr($decodedEncString, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        return sodium_crypto_secretbox_open($encString, $encNonce, $encKey);
    }

    /**
     * Decrypt string if sodium is not supported.
     *
     * @param string $encryptedString
     *
     * @return string
     */
    private static function legacy_decrypt($encryptedString)
    {
        $encMethod = 'AES-256-CBC';
        $encKey = self::getEncryptionKey();

        list($data, $iv) = explode('|', $encryptedString);
        $iv = base64_decode($iv);

        return openssl_decrypt($data, $encMethod, $encKey, 0, $iv);
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
