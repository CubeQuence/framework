<?php

namespace CQ\Middleware;

use CQ\DB\DB;
use CQ\Config\Config;
use CQ\Helpers\Request;
use CQ\Response\Json;
use CQ\Middleware\Middleware;

class RateLimit extends Middleware
{
    private $max_requests;
    private $reset_time;

    /**
     * Ratelimit API
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        $this->loadConfig($request);

        $fingerprint = $this->fingerprintRequest($request);
        $validated_request = $this->validateRequest($fingerprint);
        $headers = $this->genHeaders($validated_request);

        if (!$validated_request['valid']) {
            return $this->buildResponse($headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }

    /**
     * Get config for specific path
     *
     * @param object $request
     * 
     * @return array
     */
    protected function loadConfig($request)
    {
        $path = Request::path($request); // TODO: add path based config support

        $this->max_requests = (int) Config::get("ratelimit.max_requests") ?: 60;
        $this->reset_time = (int) Config::get("ratelimit.reset_time") ?: 60;
    }

    /**
     * Resolve request fingerprint.
     *
     * @param object $request
     *
     * @return string
     */
    protected function fingerprintRequest($request)
    {
        return sha1(
            $request->getMethod() .
                '|' . Request::path($request) .
                '|' . Request::ip()
        );
    }

    /**
     * Create a 'too many requests' response.
     *
     * @param array $headers
     *
     * @return Json
     */
    protected function buildResponse($headers)
    {
        return new Json([
            'success' => false,
            'errors' => [
                'status' => 429,
                'title' => 'ratelimit_exceeded',
                'detail' => 'Too many requests, please slow down!'
            ]
        ], 429, $headers);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param int $remaining_requests
     * @param int|null $retry_after
     *
     * @return array
     */
    protected function genHeaders($validated_request)
    {
        $headers = [
            'X-RateLimit-Limit' => $this->max_requests,
            'X-RateLimit-Remaining' => $validated_request['remaining_requests'],
            'X-RateLimit-Reset' => $validated_request['reset_time']
        ];

        return $headers;
    }

    /**
     * Calculate the number of remaining requests.
     *
     * @param string $fingerprint
     *
     * @return array
     */
    protected function validateRequest($fingerprint)
    {
        $request = DB::get('cq_ratelimit', ['counter', 'reset_time'], [
            'fingerprint' => $fingerprint
        ]);

        if (!$request) {
            $request = DB::create('cq_ratelimit', [
                'fingerprint' => $fingerprint,
                'counter' => 1,
                'reset_time' => time() + $this->reset_time
            ]);
        }

        $remaining_requests = $this->remainingRequests($fingerprint, $request);

        return [
            'valid' => $remaining_requests > 0,
            'remaining_requests' => $remaining_requests,
            'reset_time' => $request['reset_time']
        ];
    }

    /**
     * Add one or reset request counter
     *
     * @param string $fingerprint
     * @param array $reset_time
     * 
     * @return int
     */
    protected function remainingRequests($fingerprint, $request)
    {
        // reset time in past
        if (time() > $request['reset_time']) {
            DB::update('cq_ratelimit', [
                'counter' => 1,
                'reset_time' => time() + $this->reset_time
            ], [
                'fingerprint' => $fingerprint
            ]);

            return $this->max_requests - 1;
        }

        // reset time in future
        if ($request['counter'] < $this->max_requests) {
            DB::update('cq_ratelimit', [
                'counter[+]' => 1
            ], [
                'fingerprint' => $fingerprint
            ]);
        }

        return $this->max_requests - $request['counter'];
    }
}
