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
        if (AuthHelper::isValid()) {
            SessionHelper::set(
                name: 'last_activity',
                data: time()
            );

            $user = SessionHelper::get(name: 'user');
            $this->request->user = $user;

            return $next($this->request);
        }

        SessionHelper::reset();

        if ($this->requestHelper->isJson()) {
            return Respond::prettyJson(
                message: 'You have been logged out!',
                data: [
                    'redirect' => '/?msg=logout',
                ],
                code: 403
            );
        }

        SessionHelper::set(
            name: 'return_to',
            data: $this->route->getUri()
        );

        return Respond::redirect(
            url: '/?msg=logout',
            code: 403
        );
    }
}
