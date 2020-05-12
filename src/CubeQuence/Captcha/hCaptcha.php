<?php

namespace CQ\Captcha;

class hCaptcha extends Captcha
{
    /**
     * Validate reCaptchaV2
     *
     * @param string $secret
     * @param string $response
     * 
     * @return bool
     */
    protected static function v1($secret, $response)
    {
        return self::validate(
            'https://hcaptcha.com/siteverify',
            $secret,
            $response
        );
    }
}
