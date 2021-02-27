<?php

declare(strict_types=1);

namespace CQ\Helpers;

class Mail
{
    /**
     * Send form.castelnuovo.xyz.
     *
     * @param array  $data
     *
     * @throws \Throwable
     */
    public static function send(string $site_key, array $data): void
    {
        Guzzle::request(
            method: 'POST',
            url: "https://form.castelnuovo.xyz/api/{$site_key}",
            data: [
                'json' => $data,
            ]
        );
    }
}
