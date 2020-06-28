<?php

namespace CQ\Captcha;

class reCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2
     *
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    public static function v2($secret, $response)
    {
        return self::validate(
            'https://www.google.com/recaptcha/api/siteverify',
            $secret,
            $response
        );
    }
}
