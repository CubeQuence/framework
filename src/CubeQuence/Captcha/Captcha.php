<?php

namespace CQ\Captcha;

use CQ\Helpers\Request;
use CQ\Helpers\Guzzle;

class Captcha
{
    /**
     * Validate captcha.
     *
     * @param string $url
     * @param string $secret
     * @param string $response
     *
     * @return bool
     */
    protected static function validate($url, $secret, $response)
    {
        try {
            $response = Guzzle::request('POST', $url, [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => Request::ip(),
                ],
            ]);
        } catch (\Throwable $th) {
            return false;
        }

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
