<?php

namespace CQ\Response;

use Zend\Diactoros\Response\JsonResponse;

class Json extends JsonResponse
{
    /**
     * JSON Response
     *
     * @param array $data
     * @param string $controller
     * @param array $headers
     * 
     * @return void
     */
    public function __construct($data, $code, $headers = [])
    {
        parent::__construct(
            $data,
            $code,
            $headers
        );
    }
}
