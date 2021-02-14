<?php

namespace CQ\Captcha;

use CQ\Helpers\Request;
use CQ\Helpers\Guzzle;

class Captcha
{
    /**
     * Validate captcha.
     *
     * @param string $url
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    protected static function validate(string $url, string $secret, string $response) : bool
    {
        try {
            $guzzle = Guzzle::request(
                method: 'POST',
                url: $url,
                data: [
                   'form_params' => [
                        'secret' => $secret,
                        'response' => $response,
                        'remoteip' => Request::ip(),
                    ],
                ]
            );
        } catch (\Throwable) {
            return false;
        }

        return $guzzle?->data?->success ?: false;
    }
}
