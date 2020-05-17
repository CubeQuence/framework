<?php

namespace CQ\Middleware;

use CQ\Response\Json;

class RateLimit extends Middleware
{
    /**
     * Ratelimit API
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        // TODO: 1 request / 1 second / 1 ip

        $ratelimit_exceeded = false;
        if ($ratelimit_exceeded) {
            return new Json([
                'success' => false,
                'errors' => [
                    'status' => 429,
                    'title' => 'ratelimit_exceeded',
                    'detail' => 'Too many requests, please slow down!'
                ]
            ], 429);
        }

        return $next($request);
    }
}
