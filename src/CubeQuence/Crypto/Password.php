<?php

namespace CQ\Crypto;

use CQ\Config\Config;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Password as PasswordLib;
use Exception;

class Password
{
    /**
     * Get encryption key
     *
     * @param string $key
     *
     * @return \ParagonIE\Halite\Symmetric\EncryptionKey
     */
    private static function getKey($key)
    {
        $key = $key ?: Config::get('app.key');

        if (!$key) {
            throw new Exception('No key found!');
        }

        return KeyFactory::importEncryptionKey(new HiddenString($key));
    }

    /**
     * Hash and encrypt password
     *
     * @param string $plaintext_password
     * @param string $key optional
     *
     * @return string
     */
    public static function hash($plaintext_password, $key = null)
    {
        $key = self::getKey($key);

        return PasswordLib::hash(new HiddenString($plaintext_password), $key);
    }

    /**
     * Verify password
     *
     * @param HiddenString $plaintext_password
     * @param string $stored_hash
     * @param string $key optional
     *
     * @return bool
     */
    public static function verify($plaintext_password, $stored_hash, $key = null)
    {
        $key = self::getKey($key);

        try {
            return PasswordLib::verify(
                $plaintext_password,
                $stored_hash,
                $key
            );
        } catch (\ParagonIE\Halite\Alerts\InvalidMessage $ex) {
            return false;
        }
    }
}
