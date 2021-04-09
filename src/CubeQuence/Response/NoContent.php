<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\EmptyResponse;

final class NoContent extends EmptyResponse
{
    /**
     * NoContent response
     */
    public function __construct(
        int $code,
        array $headers
    ) {
        parent::__construct(
            status: $code,
            headers: $headers
        );
    }
}
