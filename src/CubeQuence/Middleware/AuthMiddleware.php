<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\AuthHelper;
use CQ\Helpers\SessionHelper;
use CQ\Response\JsonResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;

final class AuthMiddleware extends Middleware
{
    /**
     * Validate PHP session.
     */
    public function handleChild(Closure $next): Closure | JsonResponse | RedirectResponse
    {
        if (AuthHelper::valid()) {
            SessionHelper::set(
                name: 'last_activity',
                data: time()
            );

            return $next($this->request);
        }

        SessionHelper::destroy();

        if (!$this->requestHelper->isJson()) {
            SessionHelper::set(
                name: 'return_to',
                data: $this->route->getUri()
            );

            return Respond::redirect(
                url: '/?msg=logout',
                code: 403
            );
        }

        return Respond::prettyJson(
            message: 'You have been logged out!',
            data: [
                'redirect' => '/?msg=logout',
            ],
            code: 403
        );
    }
}
