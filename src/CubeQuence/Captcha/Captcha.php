<?php

namespace CQ\Captcha;

use CQ\Helpers\Request;
use CQ\Helpers\Guzzle;
use Exception;

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
        } catch (Exception $e) {
            return false;
        }

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
