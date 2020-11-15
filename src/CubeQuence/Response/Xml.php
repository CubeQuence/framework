<?php

namespace CQ\Response;

use Zend\Diactoros\Response\XmlResponse;

class Xml extends XmlResponse
{
    /**
     * XML Response.
     *
     * @param array  $data
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
