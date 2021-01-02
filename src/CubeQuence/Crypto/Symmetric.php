<?php

namespace CQ\Crypto;

use CQ\Config\Config;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Symmetric\Crypto;

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
     * @param string $key
     * @param string $type
     *
     * @return \ParagonIE\Halite\Symmetric\EncryptionKey|\ParagonIE\Halite\Symmetric\AuthenticationKey
     */
    public static function getKey($key, $type)
    {
        $key = $key ?: Config::get('app.key');

        if (!$key) {
            throw new \Throwable('No key found!');
        }

        if ($type === 'encryption') {
            return KeyFactory::importEncryptionKey(new HiddenString($key));
        }

        if ($type === 'authentication') {
            return KeyFactory::importAuthenticationKey(new HiddenString($key));
        }

        throw new \Throwable('Invalid key type!');
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
        $key = self::getKey($key, 'encryption');

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
        $key = self::getKey($key, 'encryption');

        return Crypto::decrypt(
            $enc_string,
            $key
        )->getString();
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
        $key = self::getKey($key, 'authentication');

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
        $key = self::getKey($key, 'authentication');

        return Crypto::verify(
            $message,
            $key,
            $signature
        );
    }
}
