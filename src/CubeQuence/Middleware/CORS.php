<?php

namespace CQ\Middleware;

use CQ\Config\Config;
use CQ\Response\NoContent;

class CORS extends Middleware
{
    /**
     * Add CORS headers to requests.
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => implode(', ', Config::get('cors.allow_origins', [])),
            'Access-Control-Allow-Headers' => implode(', ', Config::get('cors.allow_headers', [])),
            'Access-Control-Allow-Methods' => implode(', ', Config::get('cors.allow_methods', [])),
        ];

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        if ('POST' !== $request->getMethod()) {
            return new NoContent(204, $headers);
        }

        return $response;
    }
}
