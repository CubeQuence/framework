<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Response\HtmlResponse;
use CQ\Response\JsonResponse;
use CQ\Response\NoContentResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;

final class FormOrJSON extends Middleware
{
    /**
     * Interpret Form OR JSON
     */
    public function handleChild(Closure $next): Closure | HtmlResponse | JsonResponse | NoContentResponse | RedirectResponse
    {
        if (! $this->requestHelper->isJSON()) {
            $jsonMiddleware = new JsonMiddleware();

            return $jsonMiddleware->handle(
                request: $this->request,
                route: $this->route,
                next: $next
            );
        }

        if (! $this->requestHelper->isForm()) {
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
