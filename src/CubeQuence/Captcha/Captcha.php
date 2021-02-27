<?php

declare(strict_types=1);

namespace CQ\Captcha;

use CQ\Helpers\Guzzle;
use CQ\Helpers\Request;

class Captcha
{
    /**
     * Validate captcha.
     */
    protected static function validate(string $url, string $secret, string $response): bool
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

        return $guzzle?->data?->success ? true : false;
    }
}
