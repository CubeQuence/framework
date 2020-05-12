<?php

namespace CQ\Response;

use Zend\Diactoros\Response\JsonResponse;

class Json
{
    /**
     * JSON Response
     *
     * @param array $data
     * @param string $controller
     * 
     * @return JsonResponse
     */
    public function __construct($data, $code)
    {
        return new JsonResponse($data, $code);
    }
}
