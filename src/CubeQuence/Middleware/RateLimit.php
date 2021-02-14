<?php

namespace CQ\Middleware;

use Closure;

use CQ\DB\DB;
use CQ\Response\Json;
use CQ\Config\Config;
use CQ\Helpers\Request;

class RateLimit extends Middleware
{
    private int $max_requests;
    private int $reset_time;

    /**
     * Ratelimit API.
     *
     * @param Closure $next
     *
     * @return Closure|Json
     */
    public function handleChild(Closure $next) : Closure|Json
    {
        $this->loadConfig();

        $fingerprint = $this->fingerprintRequest();
        $validated_request = $this->validateRequest(fingerprint: $fingerprint);
        $headers = $this->genHeaders(validated_request: $validated_request);

        if (!$validated_request['valid']) {
            return $this->respond->prettyJson(
                message: 'Ratelimit Exceeded',
                code: 429,
                headers: $headers
            );
        }

        $response = $next($this->request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }

    /**
     * Get config for specific path.
     *
     * @return void
     */
    private function loadConfig() : void
    {
        $path = Request::path(request: $this->request); // TODO: add path based config support

        $this->max_requests = (int) Config::get(key: 'ratelimit.max_requests') ?: 60;
        $this->reset_time = (int) Config::get(key: 'ratelimit.reset_time') ?: 60;
    }

    /**
     * Resolve request fingerprint.
     *
     * @return string
     */
    private function fingerprintRequest() : string
    {
        return sha1(
            str: $this->request->getMethod().
                '|'.Request::path(request: $this->request). // TODO: check new methods - $route->getPath()
                '|'.Request::ip()
        );
    }

    /**
     * Add one or reset request counter.
     *
     * @param string $fingerprint
     * @param array $request_db
     *
     * @return int
     */
    private function remainingRequests(string $fingerprint, array $request_db) : int
    {
        // Reset counter if reset_time is in past
        if (time() > $request_db['reset_time']) {
            DB::update(
                table: 'cq_ratelimit',
                data: [
                    'counter' => 1,
                    'reset_time' => time() + $this->reset_time,
                ],
                where: [
                    'fingerprint' => $fingerprint,
                ]
            );

            return $this->max_requests - 1;
        }

        // Increase counter if reset_time not reached
        if ($request_db['counter'] < $this->max_requests) {
            DB::update(
                table: 'cq_ratelimit',
                data: [
                    'counter[+]' => 1,
                ],
                where: [
                    'fingerprint' => $fingerprint,
                ]
            );
        }

        return $this->max_requests - $request_db['counter'];
    }

    /**
    * Calculate the number of remaining requests.
    *
    * @param string $fingerprint
    *
    * @return array
    */
    private function validateRequest(string $fingerprint) : array
    {
        $request_db = DB::get(
            table: 'cq_ratelimit',
            columns: [
                'counter',
                'reset_time',
            ],
            where: [
                'fingerprint' => $fingerprint,
            ]
        );

        if (!$request_db) {
            $request_db = DB::create(
                table: 'cq_ratelimit',
                data: [
                    'fingerprint' => $fingerprint,
                    'counter' => 1,
                    'reset_time' => time() + $this->reset_time,
                ]
            );
        }

        $remaining_requests = $this->remainingRequests(
            fingerprint: $fingerprint,
            request_db: $request_db
        );

        return [
            'valid' => $remaining_requests > 0,
            'remaining_requests' => $remaining_requests,
            'reset_time' => $request_db['reset_time'],
        ];
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param array $validated_request // TODO: set correct type
     *
     * @return array
     */
    private function genHeaders(array $validated_request) : array
    {
        return [
            'X-RateLimit-Limit' => $this->max_requests,
            'X-RateLimit-Remaining' => $validated_request['remaining_requests'],
            'X-RateLimit-Reset' => $validated_request['reset_time'],
        ];
    }
}
