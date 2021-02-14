<?php

namespace CQ\Response;

class Redirect extends \Laminas\Diactoros\Response\RedirectResponse
{
    /**
     * Redirect response
     *
     * @param string $url
     * @param int  $code
     * @param array $headers
     */
    public function __construct(string $url, int $code, array $headers)
    {
        parent::__construct(
            uri: $url,
            status: $code,
            headers: $headers
        );
    }
}
