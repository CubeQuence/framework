<?php

namespace CQ\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Guzzle
{
    /**
     * Make guzzle http request
     *
     * @param string $method
     * @param string $url
     * @param array $data optional
     *
     * @return object
     */
    public static function request($method, $url, $data = [])
    {
        $guzzle = new Client();

        try {
            $response = $guzzle->request($method, $url, $data);
            $data = json_decode($response->getBody());

            return (object) [
                'response' => $response,
                'data' => $data,
            ];
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            throw new \Throwable(json_encode($response->errors));
        }
    }
}
