<?php

declare(strict_types=1);

namespace CQ\Crypto;

use ParagonIE\Halite\Password as PasswordLib;
use ParagonIE\HiddenString\HiddenString;

class Password
{
    /**
     * Hash and encrypt password
     *
     * @param string $context optional
     * @param string $key optional
     */
    public static function hash(string $plaintext_password, ?string $context = null, ?string $key = null): string
    {
        $plaintext_password_with_context = $plaintext_password . $context;

        if (!defined(name: 'PASSWORD_ARGON2ID')) {
            $hash = Hash::make(string: $plaintext_password_with_context);

            return Symmetric::encrypt(
                string: $hash,
                key: Symmetric::getKey(
                    type: 'encryption',
                    key: $key
                )
            );
        }

        return PasswordLib::hash(
            password: new HiddenString(value: $plaintext_password_with_context),
            secretKey: Symmetric::getKey(
                type: 'encryption',
                key: $key
            )
        );
    }

    /**
     * Verify password
     *
     * @param string $context optional
     * @param string $key optional
     */
    public static function verify(
        string $plaintext_password,
        string $encrypted_hash,
        ?string $context = null,
        ?string $key = null
    ): bool {
        $plaintext_password_with_context = new HiddenString(value: $plaintext_password . $context);

        if (!defined(name: 'PASSWORD_ARGON2ID')) {
            $hash = Symmetric::decrypt(
                enc_string: $encrypted_hash,
                key: Symmetric::getKey(
                    type: 'encryption',
                    key: $key
                )
            );

            return Hash::verify(
                check_against: $plaintext_password_with_context,
                hash: $hash
            );
        }

        try {
            return PasswordLib::verify(
                password: $plaintext_password_with_context,
                stored: $encrypted_hash,
                secretKey: Symmetric::getKey(
                    type: 'encryption',
                    key: $key
                )
            );
        } catch (\ParagonIE\Halite\Alerts\InvalidMessage) {
            return false;
        }
    }
}
