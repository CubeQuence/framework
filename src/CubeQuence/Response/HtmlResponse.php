<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\HtmlResponse as HtmlResponseDiactoros;

final class HtmlResponse extends HtmlResponseDiactoros
{
    /**
     * HTML response
     */
    public function __construct(
        string $data,
        int $code,
        array $headers
    ) {
        parent::__construct(
            html: $data,
            status: $code,
            headers: $headers
        );
    }
}
