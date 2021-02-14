<?php

namespace CQ\Helpers;

use CQ\Helpers\Guzzle;

class Mail
{
    /**
     * Send form.castelnuovo.xyz.
     *
     * @param string $site_key
     * @param array  $data
     *
     * @return void
     * @throws \Throwable
     */
    public static function send(string $site_key, array $data) : void
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
