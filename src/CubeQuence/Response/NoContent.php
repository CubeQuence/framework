<?php

namespace CQ\Response;

use Zend\Diactoros\Response\EmptyResponse;

class NoContent extends EmptyResponse
{
    /**
     * Empty Response
     *
     * @param string $controller
     * @param array $headers
     * 
     * @return void
     */
    public function __construct($code, $headers = [])
    {
        parent::__construct(
            $code,
            $headers
        );
    }
}
