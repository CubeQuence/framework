<?php

namespace CQ\Response;

use Zend\Diactoros\Response\EmptyResponse;

class NoContent extends EmptyResponse
{
    /**
     * Empty Response.
     *
     * @param string $controller
     * @param array  $headers
     * @param mixed  $code
     */
    public function __construct($code, $headers = [])
    {
        parent::__construct(
            $code,
            $headers
        );
    }
}
