<?php

declare(strict_types=1);

namespace CQ\Captcha;

final class ReCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2.
     */
    public static function v2(string $secret, string $response): bool
    {
        return self::validate(
            url: 'https://www.google.com/recaptcha/api/siteverify',
            secret: $secret,
            response: $response
        );
    }
}
