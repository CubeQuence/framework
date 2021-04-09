<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\Request;
use CQ\Response\Json as JsonResponse;

final class JSON extends Middleware
{
    /**
     * Interpret JSON and validate that the provided JSON is valid.
     */
    public function handleChild(Closure $next): Closure | JsonResponse
    {
        if (! Request::isJson(request: $this->request)) {
            return $this->respond->prettyJson(
                message: 'Invalid Content-Type',
                data: [
                    'details' => "Content-Type should be 'application/json'",
                ],
                code: 415
            );
        }

        $data = json_decode(
            json: $this->request->getBody()->getContents()
        );

        if ((json_last_error() !== JSON_ERROR_NONE)) {
            return $this->respond->prettyJson(
                message:'Problems parsing provided JSON',
                code: 415
            );
        }

        $this->request->data = $data;

        return $next($this->request);
    }
}
