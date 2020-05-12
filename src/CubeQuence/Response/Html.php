<?php

namespace CQ\Response;

use Zend\Diactoros\Response\HtmlResponse;

class Html extends HtmlResponse
{
    /**
     * HTML Response
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
