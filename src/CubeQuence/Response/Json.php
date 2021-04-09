<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\JsonResponse;

final class Json extends JsonResponse
{
    /**
     * JSON response
     */
    public function __construct(
        $data,
        int $code,
        array $headers
    ) {
        parent::__construct(
            data: $data,
            status: $code,
            headers: $headers
        );
    }
}
