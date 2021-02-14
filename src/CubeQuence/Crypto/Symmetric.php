<?php

namespace CQ\Crypto;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\Halite\Symmetric\AuthenticationKey;

use CQ\Config\Config;

class Symmetric
{
    /**
     * Generate encryption key
     *
     * @return string
     */
    public static function genKey() : string
    {
        $key = KeyFactory::generateEncryptionKey();
        $key_hex = KeyFactory::export(key: $key)->getString();

        return $key_hex;
    }

    /**
     * Get encryption key
     *
     * @param string $type
     * @param string $key optional
     *
     * @return EncryptionKey|AuthenticationKey
     * @throws \Exception
     */
    public static function getKey(string $type, ?string $key = null) : EncryptionKey|AuthenticationKey
    {
        $key = $key ?: Config::get(key: 'app.key');

        if (!$key) {
            throw new \Exception(message: 'No key found!');
        }

        if ($type === 'encryption') { // TODO: use map instead of if statements
            return KeyFactory::importEncryptionKey(
                keyData: new HiddenString(value: $key)
            );
        }

        if ($type === 'authentication') {
            return KeyFactory::importAuthenticationKey(
                keyData: new HiddenString(value: $key)
            );
        }

        throw new \Exception(message: 'Invalid key type!');
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * @param string $key optional
     *
     * @return string
     */
    public static function encrypt(string $string, ?string $key = null) : string
    {
        $key = self::getKey(
            type: 'encryption',
            key: $key
        );

        return Crypto::encrypt(
            plaintext: new HiddenString(value: $string),
            secretKey: $key
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
    public static function decrypt(string $enc_string, ?string $key = null) : string
    {
        $key = self::getKey(
            type: 'encryption',
            key: $key
        );

        return Crypto::decrypt(
            ciphertext: $enc_string,
            secretKey: $key
        )->getString();
    }

    /**
     * Sign string
     *
     * @param string $message
     * @param string $key optional
     *
     * @return string
     */
    public static function sign(string $message, ?string $key = null) : string
    {
        $key = self::getKey(
            type: 'authentication',
            key: $key
        );

        return Crypto::authenticate(
            message: $message,
            secretKey: $key
        );
    }

    /**
     * Verify string
     *
     * @param string $message
     * @param string $signature
     * @param string $key optional
     *
     * @return bool
     */
    public static function verify(string $message, string $signature, ?string $key = null) : bool
    {
        $key = self::getKey(
            type: 'authentication',
            key: $key
        );

        return Crypto::verify(
            message: $message,
            secretKey: $key,
            mac: $signature
        );
    }
}
