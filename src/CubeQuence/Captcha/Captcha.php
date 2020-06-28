<?php

namespace CQ\Captcha;

use CQ\Helpers\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Captcha
{
    /**
     * Validate captcha
     *
     * @param string $url
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    protected static function validate($url, $secret, $response)
    {
        $guzzle = new Client();

        try {
            $response = $guzzle->request('POST', $url, [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => Request::ip()
                ],
            ]);
        } catch (RequestException $e) {
            return false;
        }

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
