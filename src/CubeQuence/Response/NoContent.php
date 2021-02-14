<?php

namespace CQ\Response;

class NoContent extends \Laminas\Diactoros\Response\EmptyResponse
{
    /**
     * NoContent response
     *
     * @param int $code
     * @param array $headers
     */
    public function __construct(int $code, array $headers)
    {
        parent::__construct(
            status: $code,
            headers: $headers
        );
    }
}
