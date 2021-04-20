<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Response\JsonResponse;
use CQ\Response\Respond;

final class FormMiddleware extends Middleware
{
    /**
     * Interpret FormData
     */
    public function handleChild(Closure $next): Closure | JsonResponse
    {
        if (!$this->requestHelper->isForm()) {
            return Respond::prettyJson(
                message: 'Invalid Content-Type',
                data: [
                    'details' => "Content-Type should be 'application/x-www-form-urlencoded'",
                ],
                code: 415
            );
        }

        $data = (object) $this->request->getParsedBody();

        $this->request->data = $data;

        return $next($this->request);
    }
}
