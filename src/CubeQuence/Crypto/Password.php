<?php

namespace CQ\Crypto;

use CQ\Crypto\Hash;
use CQ\Crypto\Symmetric;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Password as PasswordLib;

class Password
{
    /**
     * Hash and encrypt password
     *
     * @param string $plaintext_password
     * @param string $context optional
     * @param string $key optional
     *
     * @return string
     */
    public static function hash($plaintext_password, $context = null, $key = null)
    {
        $plaintext_password_with_context = $plaintext_password . $context;

        if (!defined('PASSWORD_ARGON2ID')) {
            $hash = Hash::make($plaintext_password_with_context);

            return Symmetric::encrypt(
                $hash,
                Symmetric::getKey($key, 'encryption')
            );
        }

        return PasswordLib::hash(
            new HiddenString($plaintext_password_with_context),
            Symmetric::getKey($key, 'encryption')
        );
    }

    /**
     * Verify password
     *
     * @param string $plaintext_password
     * @param string $encrypted_hash
     * @param string $context optional
     * @param string $key optional
     *
     * @return bool
     */
    public static function verify($plaintext_password, $encrypted_hash, $context = null, $key = null)
    {
        $plaintext_password_with_context = new HiddenString($plaintext_password . $context);

        if (!defined('PASSWORD_ARGON2ID')) {
            $hash = Symmetric::decrypt(
                $encrypted_hash,
                Symmetric::getKey($key, 'encryption')
            );

            return Hash::verify($plaintext_password_with_context, $hash);
        }

        try {
            return PasswordLib::verify(
                $plaintext_password_with_context,
                $encrypted_hash,
                Symmetric::getKey($key, 'encryption')
            );
        } catch (\ParagonIE\Halite\Alerts\InvalidMessage $ex) {
            return false;
        }
    }
}
