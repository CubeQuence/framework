<?php

namespace CQ\Middleware;

use CQ\Helpers\Auth;
use CQ\Helpers\Request;
use CQ\Helpers\Session;
use CQ\Response\Json;
use CQ\Response\Redirect;
use MiladRahimi\PhpRouter\Middleware;

class SessionMiddleware implements Middleware
{
    /**
     * Validate PHP session.
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (!Auth::valid()) {
            Session::destroy();

            if (!Request::isJson($request)) {
                return new Redirect('/auth/logout', 403);
            }

            return new Json([
                'success' => false,
                'errors' => [
                    'status' => 403,
                    'title' => 'invalid_session',
                    'detail' => 'Session expired or IP mismatch'
                ]
            ], 403);
        }

        Session::set('last_activity', time());

        return $next($request);
    }
}
