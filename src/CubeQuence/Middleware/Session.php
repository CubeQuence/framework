<?php

namespace CQ\Middleware;

use Closure;

use CQ\Helpers\Auth;
use CQ\Response\Json;
use CQ\Helpers\Request;
use CQ\Helpers\Session as SessionHelper;
use CQ\Response\Redirect;

class Session extends Middleware
{
    /**
     * Validate PHP session.
     *
     * @param Closure $next
     *
     * @return Closure|Json
     */
    public function handleChild(Closure $next) : Closure|Json|Redirect
    {
        if (!Auth::valid()) {
            SessionHelper::destroy();

            if (!Request::isJson(request: $this->request)) {
                SessionHelper::set(
                    name: 'return_to',
                    data: Request::path(request: $this->request)
                );

                return $this->respond->redirect(
                    url: '/?msg=logout',
                    code: 403
                );
            }

            return $this->respond->prettyJson(
                message: 'You have been logged out!',
                data: [
                    'redirect' => '/?msg=logout',
                ],
                code: 403
            );
        }

        SessionHelper::set(
            name: 'last_activity',
            data:time()
        );

        return $next($this->request);
    }
}
