<?php

declare(strict_types=1);

namespace CQ\Response;

use Laminas\Diactoros\Response\XmlResponse as XmlResponseDiactoros;

final class XmlResponse extends XmlResponseDiactoros
{
    /**
     * XML response
     */
    public function __construct(
        string $data,
        int $code,
        array $headers
    ) {
        parent::__construct(
            xml: $data,
            status: $code,
            headers: $headers
        );
    }
}
