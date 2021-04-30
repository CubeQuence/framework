<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\StateHelper;
use CQ\Response\HtmlResponse;
use CQ\Response\JsonResponse;
use CQ\Response\NoContentResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;

final class CsrfMiddleware extends Middleware
{
    /**
     * Validate PHP session.
     */
    public function handleChild(Closure $next): Closure | HtmlResponse | JsonResponse | NoContentResponse | RedirectResponse
    {
        try {
            $csrfToken = $this->request->data->csrf_token;
        } catch (\Throwable) {
            return Respond::prettyJson(
                message: 'CSRF Token not found',
                code: 403
            );
        }

        if (!StateHelper::isValid(
            providedState: $csrfToken
        )) {
            return Respond::prettyJson(
                message: 'CSRF Token invalid',
                code: 403
            );
        }

        return $next($this->request);
    }
}
