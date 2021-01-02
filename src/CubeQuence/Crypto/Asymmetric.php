<?php

namespace CQ\Crypto;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\Asymmetric\EncryptionSecretKey;
use ParagonIE\Halite\Asymmetric\EncryptionPublicKey;
use ParagonIE\Halite\Asymmetric\SignatureSecretKey;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;

class Asymmetric
{
    /**
     * Generate encryption keypair
     *
     * @return object
     */
    public static function genKey()
    {
        $auth_keypair = KeyFactory::generateSignatureKeyPair();
        $auth_keys = [
            'private' => sodium_bin2hex($auth_keypair->getSecretKey()->getRawKeyMaterial()),
            'public' => sodium_bin2hex($auth_keypair->getPublicKey()->getRawKeyMaterial()),
        ];

        $enc_keypair = KeyFactory::generateEncryptionKeyPair();
        $enc_keys = [
            'private' => sodium_bin2hex($enc_keypair->getSecretKey()->getRawKeyMaterial()),
            'public' => sodium_bin2hex($enc_keypair->getPublicKey()->getRawKeyMaterial()),
        ];

        return (object) [
            'auth' => $auth_keys,
            'enc' => $enc_keys,
        ];
    }

    /**
     * Load encryption key
     *
     * @param string $key
     * @param string $type
     * @param string $scope
     *
     * @return EncryptionSecretKey|EncryptionPublicKey|SignatureSecretKey|SignaturePublicKey
     */
    public static function getKey($key, $type, $scope)
    {
        if ($type === 'encryption') {
            if ($scope === 'private') {
                return new EncryptionSecretKey(
                    new HiddenString(sodium_hex2bin($key))
                );
            }

            if ($scope === 'public') {
                return new EncryptionPublicKey(
                    new HiddenString(sodium_hex2bin($key))
                );
            }
        }

        if ($type === 'authentication') {
            if ($scope === 'private') {
                return new SignatureSecretKey(
                    new HiddenString(sodium_hex2bin($key))
                );
            }

            if ($scope === 'public') {
                return new SignaturePublicKey(
                    new HiddenString(sodium_hex2bin($key))
                );
            }
        }

        throw new \Throwable('Invalid key type!');
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * @param string $enc_public_key_receiver
     * @param string $enc_private_key_sender optional
     *
     * @return string
     */
    public static function encrypt($string, $enc_public_key_receiver, $enc_private_key_sender = null)
    {
        $enc_public_key_receiver = self::getKey($enc_public_key_receiver, 'encryption', 'public');

        if ($enc_private_key_sender) {
            $enc_private_key_sender = self::getKey($enc_private_key_sender, 'encryption', 'private');

            return Crypto::encrypt(
                new HiddenString($string),
                $enc_private_key_sender,
                $enc_public_key_receiver
            );
        }

        return Crypto::seal(
            new HiddenString($string),
            $enc_public_key_receiver
        );
    }

    /**
     * Decrypt string
     *
     * @param string $enc_string
     * @param string $enc_private_key_receiver
     * @param string $enc_public_key_sender optional
     *
     * @return string
     */
    public static function decrypt($enc_string, $enc_private_key_receiver, $enc_public_key_sender = null)
    {
        $enc_private_key_receiver = self::getKey($enc_private_key_receiver, 'encryption', 'private');

        if ($enc_public_key_sender) {
            $enc_public_key_sender = self::getKey($enc_public_key_sender, 'encryption', 'public');

            return Crypto::decrypt(
                new HiddenString($enc_string),
                $enc_private_key_receiver,
                $enc_public_key_sender
            )->getString();
        }

        return Crypto::unseal(
            $enc_string,
            $enc_private_key_receiver
        )->getString();
    }

    /**
     * Sign string
     *
     * @param string $message
     * @param string $auth_private_key
     *
     * @return string
     */
    public static function sign($message, $auth_private_key)
    {
        $auth_private_key = self::getKey($auth_private_key, 'authentication', 'private');

        return Crypto::sign(
            $message,
            $auth_private_key
        );
    }

    /**
     * Verify string
     *
     * @param string $message
     * @param string $signature
     * @param string $auth_public_key
     *
     * @return bool
     */
    public static function verify($message, $signature, $auth_public_key)
    {
        $auth_public_key = self::getKey($auth_public_key, 'authentication', 'public');

        return Crypto::verify(
            $message,
            $auth_public_key,
            $signature
        );
    }
}
