<?php

namespace CQ\Crypto;

use CQ\Config\Config;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Symmetric\Crypto;
use Exception;

class Symmetric
{
    /**
     * Generate encryption key
     *
     * @return string
     */
    public static function genKey()
    {
        $key = KeyFactory::generateEncryptionKey();
        $key_hex = KeyFactory::export($key)->getString();

        return $key_hex;
    }

    /**
     * Get encryption key
     *
     * @param string $key optional
     *
     * @return \ParagonIE\Halite\Symmetric\EncryptionKey|\ParagonIE\Halite\Symmetric\AuthenticationKey
     */
    private static function getKey($key = null)
    {
        $key = $key ?: Config::get('app.key');

        if (!$key) {
            throw new Exception('No key found!');
        }

        return KeyFactory::importEncryptionKey(new HiddenString($key));
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * @param string $key optional
     *
     * @return string
     */
    public static function encrypt($string, $key = null)
    {
        $key = self::getKey($key);

        return Crypto::encrypt(
            new HiddenString($string),
            $key
        );
    }

    /**
     * Decrypt string
     *
     * @param string $enc_string
     * @param string $key optional
     *
     * @return string
     */
    public static function decrypt($enc_string, $key = null)
    {
        $key = self::getKey($key);

        return Crypto::decrypt(
            $enc_string,
            $key
        );
    }

    /**
     * Sign string
     *
     * @param string $message
     * @param string $key
     *
     * @return string
     */
    public static function sign($message, $key = null)
    {
        $key = self::getKey($key);

        return Crypto::authenticate(
            $message,
            $key
        );
    }

    /**
     * Verify string
     *
     * @param string $message
     * @param string $signature
     * @param string $key
     *
     * @return bool
     */
    public static function verify($message, $signature, $key = null)
    {
        $key = self::getKey($key);

        return Crypto::verify(
            $message,
            $key,
            $signature
        );
    }
}
