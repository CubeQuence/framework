<?php

declare(strict_types=1);

namespace CQ\Response;

class Xml extends \Laminas\Diactoros\Response\XmlResponse
{
    /**
     * XML response
     *
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
