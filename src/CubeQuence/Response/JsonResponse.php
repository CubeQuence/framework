<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\JsonResponse as JsonResponseDiactoros;

final class JsonResponse extends JsonResponseDiactoros
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
