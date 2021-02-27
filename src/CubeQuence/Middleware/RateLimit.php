<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Config\Config;
use CQ\DB\DB;
use CQ\Helpers\Request;
use CQ\Response\Json;

class RateLimit extends Middleware
{
    private int $max_requests;
    private int $reset_time;

    /**
     * Ratelimit API.
     *
     * @return Closure|Json
     */
    public function handleChild(Closure $next): Closure | Json
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
     */
    private function loadConfig(): void
    {
        // TODO: add path based config support
        // $path = Request::path(request: $this->request);

        $this->max_requests = (int) Config::get(key: 'ratelimit.max_requests', fallback: 60);
        $this->reset_time = (int) Config::get(key: 'ratelimit.reset_time', fallback: 60);
    }

    /**
     * Resolve request fingerprint.
     */
    private function fingerprintRequest(): string
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
     * @param array $request_db
     */
    private function remainingRequests(string $fingerprint, array $request_db): int
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
     * @return array
     */
    private function validateRequest(string $fingerprint): array
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
    private function genHeaders(array $validated_request): array
    {
        return [
            'X-RateLimit-Limit' => $this->max_requests,
            'X-RateLimit-Remaining' => $validated_request['remaining_requests'],
            'X-RateLimit-Reset' => $validated_request['reset_time'],
        ];
    }
}
