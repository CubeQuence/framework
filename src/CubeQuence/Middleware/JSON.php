<?php

namespace CQ\Middleware;

use CQ\Helpers\Request;
use CQ\Response\Json as JsonResponse;
use MiladRahimi\PhpRouter\Middleware;

class JSON implements Middleware
{
    /**
     * If POST,PUT,PATCH requests contains JSON interpret it
     * Also validate that the provided JSON is valid.
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            if (!Request::isJson($request)) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => [
                        'status' => 415,
                        'title' => 'invalid_content_type',
                        'detail' => "Content-Type should be 'application/json'"
                    ]
                ], 415);
            }

            $data = json_decode($request->getBody()->getContents());

            if ((JSON_ERROR_NONE !== json_last_error())) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => [
                        'status' => 415,
                        'title' => 'invalid_json',
                        'detail' => 'Problems parsing provided JSON'
                    ]
                ], 415);
            }

            $request->data = $data;
        }

        return $next($request);
    }
}
