<?php

namespace CQ\Response;

class Xml extends \Laminas\Diactoros\Response\XmlResponse
{
    /**
     * XML response
     *
     * @param string $data
     * @param int $code
     * @param array $headers
     */
    public function __construct(string $data, int $code, array $headers)
    {
        parent::__construct(
            xml: $data,
            status: $code,
            headers: $headers
        );
    }
}
