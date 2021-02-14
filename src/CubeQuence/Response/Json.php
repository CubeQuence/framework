<?php

namespace CQ\Response;

class Json extends \Laminas\Diactoros\Response\JsonResponse
{
    /**
     * JSON response
     *
     * @param mixed $data
     * @param int $code
     * @param array $headers
     */
    public function __construct($data, int $code, array $headers)
    {
        parent::__construct(
            data: $data,
            status: $code,
            headers: $headers
        );
    }
}
