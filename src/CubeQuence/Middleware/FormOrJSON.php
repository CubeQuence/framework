<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Middleware\JsonMiddleware;
use CQ\Middleware\FormMiddleware;
use CQ\Response\JsonResponse;
use CQ\Response\Respond;

final class FormOrJSON extends Middleware
{
    /**
     * Interpret Form OR JSON
     */
    public function handleChild(Closure $next): Closure | JsonResponse
    {
        if (!$this->requestHelper->isJSON()) {
            $jsonMiddleware = new JsonMiddleware();

            return $jsonMiddleware->handle(
                request: $this->request,
                route: $this->route,
                next: $next
            );
        }

        if (!$this->requestHelper->isForm()) {
            $formMiddleware = new FormMiddleware();

            return $formMiddleware->handle(
                request: $this->request,
                route: $this->route,
                next: $next
            );
        }

        return Respond::prettyJson(
            message: 'Invalid Content-Type',
            data: [
                'details' => "Content-Type should be 'application/x-www-form-urlencoded' or 'application/json'",
            ],
            code: 415
        );
    }
}
