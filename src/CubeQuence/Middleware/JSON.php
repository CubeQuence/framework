<?php

namespace CQ\Middleware;

use CQ\Helpers\Request;
use CQ\Response\Json as JsonResponse;
use CQ\Middleware\Middleware;

class JSON extends Middleware
{
    /**
     * If POST,PUT,PATCH,DELETE requests contains JSON interpret it
     * Also validate that the provided JSON is valid.
     *
     * @param object $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!Request::isJson($request)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid Content-Type',
                    'data' => [
                        'details' => "Content-Type should be 'application/json'"
                    ]
                ], 415);
            }

            $data = json_decode($request->getBody()->getContents());

            if ((JSON_ERROR_NONE !== json_last_error())) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Problems parsing provided JSON'
                ], 415);
            }

            $request->data = $data;
        }

        return $next($request);
    }
}
