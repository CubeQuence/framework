<?php

namespace CQ\Crypto;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\Asymmetric\EncryptionSecretKey;
use ParagonIE\Halite\Asymmetric\EncryptionPublicKey;
use ParagonIE\Halite\Asymmetric\SignatureSecretKey;
use ParagonIE\Halite\Asymmetric\SignaturePublicKey;
use Exception;

class Asymmetric
{
    /**
     * Generate encryption keypair
     *
     * @param string $type
     *
     * @return array
     */
    public static function genKey($type)
    {
        if ($type === 'encryption') {
            $keypair = KeyFactory::generateEncryptionKeyPair();
        }

        if ($type === 'authentication') {
            $keypair = KeyFactory::generateSignatureKeyPair();
        }

        if (!$keypair) {
            throw new Exception('Invalid key type!');
        }

        $private_key = $keypair->getSecretKey()->getRawKeyMaterial();
        $public_key = $keypair->getPublicKey()->getRawKeyMaterial();

        return [
            'private_key' => sodium_bin2hex($private_key),
            'public_key' => sodium_bin2hex($public_key),
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

        throw new Exception('Invalid key type!');
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * @param string $public_key_receiver
     * @param string $private_key_sender optional
     *
     * @return string
     */
    public static function encrypt($string, $public_key_receiver, $private_key_sender = null)
    {
        $public_key_receiver = self::getKey($public_key_receiver, 'encryption', 'public');

        if ($private_key_sender) {
            $private_key_sender = self::getKey($private_key_sender, 'encryption', 'private');

            return Crypto::encrypt(
                new HiddenString($string),
                $private_key_sender,
                $public_key_receiver
            );
        }

        return Crypto::seal(
            new HiddenString($string),
            $public_key_receiver
        );
    }

    /**
     * Decrypt string
     *
     * @param string $enc_string
     * @param string $private_key_receiver
     * @param string $public_key_sender optional
     *
     * @return string
     */
    public static function decrypt($enc_string, $private_key_receiver, $public_key_sender = null)
    {
        $private_key_receiver = self::getKey($private_key_receiver, 'encryption', 'private');

        if ($public_key_sender) {
            $public_key_sender = self::getKey($public_key_sender, 'encryption', 'public');

            return Crypto::decrypt(
                new HiddenString($enc_string),
                $private_key_receiver,
                $public_key_sender
            )->getString();
        }

        return Crypto::unseal(
            $enc_string,
            $private_key_receiver
        )->getString();
    }

    /**
     * Sign string
     *
     * @param string $message
     * @param string $private_key
     *
     * @return string
     */
    public static function sign($message, $private_key)
    {
        $private_key = self::getKey($private_key, 'authentication', 'private');

        return Crypto::sign(
            $message,
            $private_key
        );
    }

    /**
     * Verify string
     *
     * @param string $message
     * @param string $signature
     * @param string $public_key
     *
     * @return bool
     */
    public static function verify($message, $signature, $public_key)
    {
        $public_key = self::getKey($public_key, 'authentication', 'public');

        return Crypto::verify(
            $message,
            $public_key,
            $signature
        );
    }
}
