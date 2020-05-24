<?php

namespace CQ\Middleware;

use CQ\Helpers\Auth;
use CQ\Helpers\Request;
use CQ\Helpers\Session as SessionHelper;
use CQ\Response\Json;
use CQ\Response\Redirect;
use CQ\Middleware\Middleware;

class Session extends Middleware
{
    /**
     * Validate PHP session.
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (!Auth::valid()) {
            SessionHelper::destroy();

            if (!Request::isJson($request)) {
                return new Redirect('/auth/logout', 403);
            }

            return new Json([
                'success' => false,
                'message' => 'Invalid Session',
                'data' => [
                    'redirect' => '/'
                ]
            ], 403);
        }

        SessionHelper::set('last_activity', time());

        return $next($request);
    }
}
