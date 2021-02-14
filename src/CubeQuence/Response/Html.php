<?php

namespace CQ\Response;

class Html extends \Laminas\Diactoros\Response\HtmlResponse
{
    /**
     * HTML response
     *
     * @param string $data
     * @param int $code
     * @param array $headers
     */
    public function __construct(string $data, int $code, array $headers)
    {
        parent::__construct(
            html: $data,
            status: $code,
            headers: $headers
        );
    }
}
