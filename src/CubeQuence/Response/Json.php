<?php

declare(strict_types=1);

namespace CQ\Response;

class Json extends \Laminas\Diactoros\Response\JsonResponse
{
    /**
     * JSON response
     *
     * @param mixed $data
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
