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
    public static function genKey() : object
    {
        $auth_keypair = KeyFactory::generateSignatureKeyPair();
        $auth_keys = [
            'private' => sodium_bin2hex(
                binary: $auth_keypair->getSecretKey()->getRawKeyMaterial()
            ),
            'public' => sodium_bin2hex(
                binary: $auth_keypair->getPublicKey()->getRawKeyMaterial()
            ),
        ];

        $enc_keypair = KeyFactory::generateEncryptionKeyPair();
        $enc_keys = [
            'private' => sodium_bin2hex(
                binary: $enc_keypair->getSecretKey()->getRawKeyMaterial()
            ),
            'public' => sodium_bin2hex(
                binary: $enc_keypair->getPublicKey()->getRawKeyMaterial()
            ),
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
     * @throws \Exception
     */
    public static function getKey(
        string $key,
        string $type,
        string $scope
    ) : EncryptionSecretKey|EncryptionPublicKey|SignatureSecretKey|SignaturePublicKey {
        if ($type === 'encryption') {
            if ($scope === 'private') {
                return new EncryptionSecretKey(
                    keyMaterial: new HiddenString(sodium_hex2bin(hex: $key))
                );
            }

            if ($scope === 'public') {
                return new EncryptionPublicKey(
                    keyMaterial: new HiddenString(sodium_hex2bin(hex: $key))
                );
            }
        }

        if ($type === 'authentication') {
            if ($scope === 'private') {
                return new SignatureSecretKey(
                    keyMaterial: new HiddenString(sodium_hex2bin(hex: $key))
                );
            }

            if ($scope === 'public') {
                return new SignaturePublicKey(
                    keyMaterial: new HiddenString(sodium_hex2bin(hex: $key))
                );
            }
        }

        throw new \Exception(message: 'Invalid key type!');
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
    public static function encrypt(
        string $string,
        string $enc_public_key_receiver,
        ?string $enc_private_key_sender = null
    ) : string {
        $enc_public_key_receiver = self::getKey(
            key: $enc_public_key_receiver,
            type: 'encryption',
            scope: 'public'
        );

        if ($enc_private_key_sender) {
            $enc_private_key_sender = self::getKey(
                key: $enc_private_key_sender,
                type: 'encryption',
                scope: 'private'
            );

            return Crypto::encrypt(
                plaintext: new HiddenString($string),
                ourPrivateKey: $enc_private_key_sender,
                theirPublicKey: $enc_public_key_receiver
            );
        }

        return Crypto::seal(
            plaintext: new HiddenString(value: $string),
            publicKey: $enc_public_key_receiver
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
    public static function decrypt(
        string $enc_string,
        string $enc_private_key_receiver,
        ?string $enc_public_key_sender = null
    ) : string {
        $enc_private_key_receiver = self::getKey(
            key: $enc_private_key_receiver,
            type: 'encryption',
            scope: 'private'
        );

        if ($enc_public_key_sender) {
            $enc_public_key_sender = self::getKey(
                key: $enc_public_key_sender,
                type: 'encryption',
                scope: 'public'
            );

            return Crypto::decrypt(
                ciphertext: new HiddenString(value: $enc_string),
                ourPrivateKey: $enc_private_key_receiver,
                theirPublicKey: $enc_public_key_sender
            )->getString();
        }

        return Crypto::unseal(
            ciphertext: $enc_string,
            privateKey: $enc_private_key_receiver
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
    public static function sign(string $message, string $auth_private_key) : string
    {
        $auth_private_key = self::getKey(
            key: $auth_private_key,
            type: 'authentication',
            scope: 'private'
        );

        return Crypto::sign(
            message: $message,
            privateKey: $auth_private_key
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
    public static function verify(string $message, string $signature, string $auth_public_key) : bool
    {
        $auth_public_key = self::getKey(
            key: $auth_public_key,
            type: 'authentication',
            scope: 'public'
        );

        return Crypto::verify(
            message: $message,
            publicKey: $auth_public_key,
            signature: $signature
        );
    }
}
