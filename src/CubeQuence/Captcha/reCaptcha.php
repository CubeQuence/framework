<?php

namespace CQ\Captcha;

class reCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2.
     *
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    public static function v2(string $secret, string $response) : bool
    {
        return self::validate(
            url: 'https://www.google.com/recaptcha/api/siteverify',
            secret: $secret,
            response: $response
        );
    }
}
