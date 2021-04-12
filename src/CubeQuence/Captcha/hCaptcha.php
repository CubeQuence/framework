<?php

declare(strict_types=1);

namespace CQ\Captcha;

final class HCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2.
     */
    public static function v1(string $secret, string $response): bool
    {
        return self::validate(
            url: 'https://hcaptcha.com/siteverify',
            secret: $secret,
            response: $response
        );
    }
}
