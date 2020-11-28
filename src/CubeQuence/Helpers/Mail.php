<?php

namespace CQ\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Mail
{
    /**
     * Send form.castelnuovo.xyz.
     *
     * @param string $site_key
     * @param array  $data
     *
     * @throws Exception
     */
    public static function send($site_key, $data)
    {
        $guzzle = new Client();

        try {
            $guzzle->post("https://form.castelnuovo.xyz/api/{$site_key}", [
                'json' => $data,
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            throw new Exception(json_encode($response->errors));
        }
    }
}
