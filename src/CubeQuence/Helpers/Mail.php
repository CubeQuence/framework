<?php

namespace CQ\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Mail
{
    /**
     * Send mailjs.lucacastelnuovo.nl
     *
     * @param string $access_token
     * @param array $data
     * @param string $origin
     *
     * @throws Exception
     */
    public static function send($access_token, $data, $origin)
    {
        $guzzle = new Client();

        try {
            $guzzle->post('https://mailjs.lucacastelnuovo.nl/submit', [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}",
                    'Origin' => $origin
                ],
                'json' => $data,
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            throw new Exception(json_encode($response->errors));
        }
    }
}
