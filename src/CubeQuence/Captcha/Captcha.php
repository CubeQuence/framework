<?php

declare(strict_types=1);

namespace CQ\Captcha;

use CQ\Helpers\Request as RequestHelper;
use CQ\Request\Request;

class Captcha
{
    /**
     * Validate captcha.
     */
    protected static function validate(string $url, string $secret, string $response): bool
    {
        try {
            $response = Request::send(
                method: 'POST',
                path: $url,
                form: [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => RequestHelper::ip(),
                ]
            );
        } catch (\Throwable) {
            return false;
        }

        return $response?->success ? true : false;
    }
}
