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
        try {
            $response = Guzzle::request('GET', 'https://api.castelnuovo.xyz/secrets', [
                'headers' => [
                    'X-Api-Key' => Config::get('api.key'),
            ], ]);
        } catch (\Throwable $e) {
            return [];
        }

        $response = json_decode($response->getBody());

        return $response;
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
        try {
            $response = Guzzle::request('POST', 'https://api.castelnuovo.xyz/secrets', [
                'headers' => [
                    'X-Api-Key' => Config::get('api.key'),
                ],
                'json' => [
                    'name' => $name,
                    'data' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return [];
        }

        $response = json_decode($response->getBody());

        return $response;
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

        try {
            $response = Guzzle::request('POST', "https://api.castelnuovo.xyz/secrets/{$id}", [
                'headers' => [
                    'X-Api-Key' => Config::get('api.key'),
                ],
                'json' => [
                    'key' => $key,
                ],
            ]);
        } catch (\Throwable $e) {
            return [];
        }

        $response = json_decode($response->getBody());

        return $response;
    }

    /**
     * updateStore
     *
     * @param array|object $data
     * @param string $id optional
     * @param string $key optional
     *
     * @return object
     *
     * @throws \Throwable
     */

    public static function updateStore($data, $id = null, $key = null)
    {
        $id = $id ?: Config::get('secrets.id');
        $key = $key ?: Config::get('secrets.key');

        try {
            $response = Guzzle::request('PUT', "https://api.castelnuovo.xyz/secrets/{$id}", [
                'headers' => [
                    'X-Api-Key' => Config::get('api.key'),
                ],
                'json' => [
                    'key' => $key,
                    'data' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return [];
        }

        $response = json_decode($response->getBody());

        return $response;
    }

    /**
    * deleteStore
    *
    * @param string $id optional
    *
    * @return object
    *
    * @throws \Throwable
    */
    public static function deleteStore($id = null)
    {
        $id = $id ?: Config::get('secrets.id');

        try {
            $response = Guzzle::request('DELETE', "https://api.castelnuovo.xyz/secrets/{$id}", [
                'headers' => [
                    'X-Api-Key' => Config::get('api.key'),
                ],
            ]);
        } catch (\Throwable $e) {
            return [];
        }

        $response = json_decode($response->getBody());

        return $response;
    }
}
