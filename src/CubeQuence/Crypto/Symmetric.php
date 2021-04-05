<?php

declare(strict_types=1);

namespace CQ\Crypto;

use CQ\Config\Config;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\AuthenticationKey;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;

class Symmetric
{
    /**
     * Generate encryption key
     */
    public static function genKey(): string
    {
        $key = KeyFactory::generateEncryptionKey();

        return KeyFactory::export(key: $key)->getString();
    }

    /**
     * Get encryption key
     *
     * @param string $key optional
     *
     * @return EncryptionKey|AuthenticationKey
     *
     * @throws \Exception
     */
    public static function getKey(string $type, ?string $key = null): EncryptionKey | AuthenticationKey
    {
        $key = $key ? $key : Config::get(key: 'app.key');

        if (! $key) {
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
     * @param string $key optional
     */
    public static function encrypt(string $string, ?string $key = null): string
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
     * @param string $key optional
     */
    public static function decrypt(string $enc_string, ?string $key = null): string
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
     * @param string $key optional
     */
    public static function sign(string $message, ?string $key = null): string
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
     * @param string $key optional
     */
    public static function verify(string $message, string $signature, ?string $key = null): bool
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
