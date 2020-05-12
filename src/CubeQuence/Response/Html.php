<?php

namespace CQ\Response;

use Zend\Diactoros\Response\HtmlResponse;

class Html
{
    /**
     * Html Response
     *
     * @param array $data
     * @param string $controller
     * 
     * @return HtmlResponse
     */
    public function __construct($data, $code)
    {
        return new HtmlResponse($data, $code);
    }
}
