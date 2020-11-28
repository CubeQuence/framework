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
     * @param string $key optional
     *
     * @return string
     */
    public static function encrypt($string, $key = null)
    {
        $key = $key ?: self::getKey();

        if (!function_exists('sodium_crypto_secretbox')) {
            return self::legacy_encrypt($string, $key);
        }

        $nonce = Str::random(24);
        $encString = sodium_crypto_secretbox($string, $nonce, $key);

        return base64_encode($encString) . '|' . base64_encode($nonce);
    }

    /**
     * Encrypt string if sodium is not supported.
     *
     * @param string $string
     * @param string $key
     *
     * @return string
     */
    private static function legacy_encrypt($string, $key)
    {
        $encMethod = 'AES-256-CBC';

        $ivLength = openssl_cipher_iv_length($encMethod);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encString = openssl_encrypt($string, $encMethod, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encString) . '|' . base64_encode($iv);
    }

    /**
     * Decrypt string.
     *
     * @param string $encryptedString
     * @param string $key
     *
     * @return string
     */
    public static function decrypt($encString, $key = null)
    {
        $key = self::getKey();

        if (!function_exists('sodium_crypto_secretbox_open')) {
            return self::legacy_decrypt($encString, $key);
        }

        list($decodedEncString, $nonce) = explode('|', $encString);
        $decodedEncString = base64_decode($decodedEncString);
        $nonce = base64_decode($nonce);

        return sodium_crypto_secretbox_open($decodedEncString, $nonce, $key);
    }

    /**
     * Decrypt string if sodium is not supported.
     *
     * @param string $encString
     * @param string $key
     *
     * @return string
     */
    private static function legacy_decrypt($encString, $key)
    {
        $encMethod = 'AES-256-CBC';

        list($data, $iv) = explode('|', $encString);
        $iv = base64_decode($iv);

        return openssl_decrypt($data, $encMethod, $key, 0, $iv);
    }

    /**
     * Get key from config.
     *
     * @return string
     */
    private static function getKey()
    {
        $key = Config::get('app.key');

        if (!$key) {
            throw new Exception('No key found!');
        }

        return $key;
    }
}
