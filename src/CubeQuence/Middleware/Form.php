<?php

namespace CQ\Middleware;

use CQ\Middleware\Middleware;
use CQ\Helpers\Request;
use CQ\Response\Json as JsonResponse;

class Form extends Middleware
{
    /**
     * If POST,PUT,PATCH,DELETE requests contains FormData interpret it
     *
     * @param object $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!Request::isForm($request)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid Content-Type',
                    'data' => [
                        'details' => "Content-Type should be 'application/x-www-form-urlencoded'",
                    ],
                ], 415);
            }

            $data = (object) $request->getParsedBody();

            $request->data = $data;
        }

        return $next($request);
    }
}
