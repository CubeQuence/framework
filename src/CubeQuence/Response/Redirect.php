<?php

declare(strict_types=1);

namespace CQ\Response;

class Redirect extends \Laminas\Diactoros\Response\RedirectResponse
{
    /**
     * Redirect response
     *
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
