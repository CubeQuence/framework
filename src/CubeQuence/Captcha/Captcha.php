<?php

declare(strict_types=1);

namespace CQ\Captcha;

use CQ\Request\Request;

abstract class Captcha
{
    /**
     * Validate captcha.
     */
    protected static function validate(
        string $url,
        string $secret,
        string $response
    ): bool {
        try {
            $response = Request::send(
                method: 'POST',
                path: $url,
                form: [
                    'secret' => $secret,
                    'response' => $response,
                ]
            );
        } catch (\Throwable) {
            return false;
        }

        return $response?->success ? true : false;
    }
}
