<?php

namespace CQ\Response;

use Zend\Diactoros\Response\RedirectResponse;

class Redirect extends RedirectResponse
{
    /**
     * Redirect Response.
     *
     * @param array  $data
     * @param string $controller
     * @param array  $headers
     * @param mixed  $code
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
