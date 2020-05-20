<?php

namespace CQ\Middleware;

use CQ\DB\DB;
use CQ\Config\Config;
use CQ\Response\Json;

class RateLimit extends Middleware
{
    private $max_requests;

    /**
     * Define middleware variables
     * 
     * @return void
     */
    public function __construct()
    {
        $this->max_requests = (int) Config::get('ratelimit.max_requests') ?: 60;
        $this->reset_time = (int) Config::get('ratelimit.reset_time') ?: 60;
    }

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
        $fingerprint = $this->fingerprintRequest($request);
        $validated_request = $this->validateRequest($fingerprint);

        if (!$validated_request['valid']) {
            return $this->buildResponse($validated_request);
        }

        $headers = $this->genHeaders($validated_request);

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
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
                '|' . $request->getUri() .
                '|' . $_SERVER['REMOTE_ADDR']
        );
    }

    /**
     * Create a 'too many requests' response.
     *
     * @param array $validated_request
     *
     * @return Json
     */
    protected function buildResponse($validated_request)
    {
        $headers = $this->genHeaders($validated_request,);

        return new Json([
            'success' => false,
            'data' => $headers,
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
            'X-RateLimit-Remaining' => $validated_request['remaining_requests']
        ];

        if (!$validated_request['valid']) {
            $headers['X-RateLimit-Reset'] = $validated_request['reset_time'];
        }

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

        $counter = $this->incrementRequest(
            $fingerprint,
            $request['reset_time'],
            $request['counter']
        );

        return [
            'valid' => ($this->max_requests - $counter) > 0,
            'remaining_requests' => $this->max_requests - $counter,
            'reset_time' => $request['reset_time']
        ];
    }

    /**
     * Add one or reset request counter
     *
     * @param string $fingerprint
     * @param int $reset_time
     * @param int $current_counter
     * 
     * @return int
     */
    protected function incrementRequest($fingerprint, $reset_time, $current_counter)
    {
        // reset time in past
        if (time() > $reset_time) {
            DB::update('cq_ratelimit', [
                'counter' => 1,
                'reset_time' => time() + $this->reset_time
            ], [
                'fingerprint' => $fingerprint
            ]);

            return 1;
        }

        // reset time in future
        if ($current_counter < $this->max_requests) {
            DB::update('cq_ratelimit', [
                'counter[+]' => 1
            ], [
                'fingerprint' => $fingerprint
            ]);
        }

        return $current_counter++;
    }
}
