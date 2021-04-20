<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\ConfigHelper;
use CQ\Response\NoContentResponse;
use CQ\Response\Respond;

final class CorsMiddleware extends Middleware
{
    /**
     * Add CORS headers to requests.
     */
    public function handleChild(Closure $next): Closure | NoContentResponse
    {
        $headers = [
            'Access-Control-Allow-Origin' => implode(
                glue: ', ',
                pieces: ConfigHelper::get(key: 'cors.allow_origins', fallback: [])
            ),
            'Access-Control-Allow-Headers' => implode(
                glue: ', ',
                pieces: ConfigHelper::get(key: 'cors.allow_headers', fallback: [])
            ),
            'Access-Control-Allow-Methods' => implode(
                glue: ', ',
                pieces: ConfigHelper::get(key: 'cors.allow_methods', fallback: [])
            ),
        ];

        $response = $next($this->request);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        if ($this->request->getMethod() === 'OPTIONS') {
            Respond::noContent(
                headers: $headers
            );
        }

        return $response;
    }
}
