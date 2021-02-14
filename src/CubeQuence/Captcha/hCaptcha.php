<?php

namespace CQ\Captcha;

class hCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2.
     *
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    public static function v1(string $secret, string $response) : bool
    {
        return self::validate(
            url: 'https://hcaptcha.com/siteverify',
            secret: $secret,
            response: $response
        );
    }
}
