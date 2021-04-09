<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Request\Request;

class Mail
{
    /**
     * Send form.castelnuovo.xyz.
     */
    public static function send(string $site_key, array $data): void
    {
        Request::send(
            method: 'POST',
            path: "https://form.castelnuovo.xyz/api/{$site_key}",
            json: $data
        );
    }
}
