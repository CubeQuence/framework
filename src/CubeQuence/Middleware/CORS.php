<?php

namespace CQ\Middleware;

use Closure;

use CQ\Config\Config;
use CQ\Response\Json;
use CQ\Response\NoContent;

class CORS extends Middleware
{
    /**
     * Add CORS headers to requests.
     *
     * @param Closure $next
     *
     * @return Closure|Json
     */
    public function handleChild(Closure $next) : Closure|Json
    {
        $headers = [
            'Access-Control-Allow-Origin' => implode(
                glue:', ',
                pieces: Config::get(key: 'cors.allow_origins', fallback: [])
            ),
            'Access-Control-Allow-Headers' => implode(
                glue:', ',
                pieces: Config::get(key: 'cors.allow_headers', fallback: [])
            ),
            'Access-Control-Allow-Methods' => implode(
                glue:', ',
                pieces: Config::get(key: 'cors.allow_methods', fallback: [])
            ),
        ];

        $response = $next($this->request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        if ($this->request->getMethod() === 'OPTIONS') {
            return new NoContent(
                code: 204,
                headers: $headers
            );
        }

        return $response;
    }
}
