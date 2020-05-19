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
        $this->max_requests = Config::get('ratelimit.max_requests') ?: 60;
        $this->reset_time = Config::get('ratelimit.reset_time') ?: 60;
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
        $signature = $this->resolveRequestSignature($request);
        $ratelimit_exceeded = true;

        if ($ratelimit_exceeded) {
            return $this->buildResponse($signature);
        }

        $headers = $this->genHeaders(
            $this->calculateRemainingRequests($signature)
        );
        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }

    /**
     * Resolve request signature.
     *
     * @param object $request
     *
     * @return string
     */
    protected function resolveRequestSignature($request)
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
     * @param  string $signature
     *
     * @return Json
     */
    protected function buildResponse($signature)
    {
        $headers = $this->genHeaders(
            $this->calculateRemainingRequests($signature),
            // TODO: $this->limiter->avalibleIn($signature)
        );

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
    protected function genHeaders($remaining_requests, $retry_after = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $this->max_requests,
            'X-RateLimit-Remaining' => $remaining_requests
        ];

        if (!is_null($retry_after)) {
            $headers['Retry-After'] = $retry_after;
        }

        return $headers;
    }

    /**
     * Calculate the number of remaining requests.
     *
     * @param string $signature
     *
     * @return int
     */
    protected function calculateRemainingRequests($signature)
    {
        $request = DB::get('cq_ratelimit', ['counter', 'reset_time'], [
            'signature' => $signature
        ]);

        if (!$request) {
            $request = DB::create('cq_ratelimti', [
                'signature' => $signature,
                'counter' => 1,
                'reset_time' => time() + $this->reset_time
            ]);
        }

        // reset time in past
        if (time() > $request['reset_time']) {
            DB::update('cq_ratelimti', [
                'counter' => 1,
                'reset_time' => time() + $this->reset_time
            ], [
                'signature' => $signature
            ]);

            $counter = 1;
        }

        // reset time in future
        if (time() > $request['reset_time']) {
            DB::update('cq_ratelimti', [
                'counter[+]' => 1
            ], [
                'signature' => $signature
            ]);

            $counter = $request['counter']++;
        }

        $remaining_requests = $this->max_requests - $counter;

        return $remaining_requests;
    }
}
