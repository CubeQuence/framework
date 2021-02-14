<?php

namespace CQ\Middleware;

use Closure;

use CQ\Response\Json as JsonResponse;
use CQ\Helpers\Request;

class JSON extends Middleware
{
    /**
     * Interpret JSON and validate that the provided JSON is valid.
     *
     * @param Closure $next
     *
     * @return Closure|JsonResponse
     */
    public function handleChild(Closure $next) : Closure|JsonResponse
    {
        if (!Request::isJson(request: $this->request)) {
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

        if ((JSON_ERROR_NONE !== json_last_error())) {
            return $this->respond->prettyJson(
                message:'Problems parsing provided JSON',
                code: 415
            );
        }

        $this->request->data = $data;

        return $next($this->request);
    }
}
