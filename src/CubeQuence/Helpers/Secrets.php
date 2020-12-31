<?php

namespace CQ\Helpers;

use CQ\Helpers\Guzzle;
use CQ\Config\Config;

class Secrets
{
    /**
     * listStores
     *
     * @return object
     *
     * @throws \Throwable
     */
    public static function listStores()
    {
        $response = Guzzle::request('GET', 'https://api.castelnuovo.xyz/secrets', [
            'headers' => [
                'X-Api-Key' => Config::get('api.key'),
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->data;
    }

    /**
     * createStore
     *
     * @return object
     *
     * @throws \Throwable
     */
    public static function createStore($name, $data)
    {
        $response = Guzzle::request('POST', 'https://api.castelnuovo.xyz/secrets', [
            'headers' => [
                'X-Api-Key' => Config::get('api.key'),
            ],
            'json' => [
                'name' => $name,
                'data' => $data,
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->data;
    }

    /**
     * getStore
     *
     * @param string $id optional
     * @param string $key optional
     *
     * @return object
     *
     * @throws \Throwable
     */
    public static function getStore($id = null, $key = null)
    {
        $id = $id ?: Config::get('secrets.id');
        $key = $key ?: Config::get('secrets.key');

        $response = Guzzle::request('POST', "https://api.castelnuovo.xyz/secrets/{$id}", [
            'headers' => [
                'X-Api-Key' => Config::get('api.key'),
            ],
            'json' => [
                'key' => $key,
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->data;
    }

    /**
     * updateStore
     *
     * @param array|object $data
     * @param string $id optional
     * @param string $key optional
     *
     * @return void
     *
     * @throws \Throwable
     */

    public static function updateStore($data, $id = null, $key = null)
    {
        $id = $id ?: Config::get('secrets.id');
        $key = $key ?: Config::get('secrets.key');

        Guzzle::request('PUT', "https://api.castelnuovo.xyz/secrets/{$id}", [
            'headers' => [
                'X-Api-Key' => Config::get('api.key'),
            ],
            'json' => [
                'key' => $key,
                'data' => $data,
            ],
        ]);
    }

    /**
    * deleteStore
    *
    * @param string $id optional
    *
    * @return void
    *
    * @throws \Throwable
    */
    public static function deleteStore($id = null)
    {
        $id = $id ?: Config::get('secrets.id');

        Guzzle::request('DELETE', "https://api.castelnuovo.xyz/secrets/{$id}", [
            'headers' => [
                'X-Api-Key' => Config::get('api.key'),
            ],
        ]);
    }
}
