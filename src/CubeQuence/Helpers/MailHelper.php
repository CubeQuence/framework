<?php

declare(strict_types=1);

namespace CQ\Helpers;

use CQ\Request\Request;

final class MailHelper
{
    /**
     * Send form.castelnuovo.xyz
     */
    public static function send(string $siteKey, array $data): void
    {
        Request::send(
            method: 'POST',
            path: "https://form.castelnuovo.xyz/api/{$siteKey}",
            json: $data
        );
    }
}
