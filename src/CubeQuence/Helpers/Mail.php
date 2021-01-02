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
     * @throws \Throwable
     */
    public static function send($site_key, $data)
    {
        Guzzle::request(
            'POST',
            "https://form.castelnuovo.xyz/api/{$site_key}",
            ['json' => $data]
        );
    }
}
