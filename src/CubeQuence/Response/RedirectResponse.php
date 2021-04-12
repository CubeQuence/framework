<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\RedirectResponse as RedirectResponseDiactoros;

final class RedirectResponse extends RedirectResponseDiactoros
{
    /**
     * Redirect response
     */
    public function __construct(
        string $url,
        int $code,
        array $headers
    ) {
        parent::__construct(
            uri: $url,
            status: $code,
            headers: $headers
        );
    }
}
