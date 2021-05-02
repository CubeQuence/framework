<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Helpers\AppHelper;
use CQ\Helpers\AuthHelper;
use CQ\Helpers\ConfigHelper;
use CQ\Helpers\SessionHelper;
use CQ\OAuth\Flows\Provider\AuthorizationCode;
use CQ\Response\JsonResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

final class AuthCodeController extends AuthController
{
    /**
     * Auth provider config
     */
    public function __construct(
        ServerRequestInterface $request,
        Route $route,
    ) {
        parent::__construct(
            flowProvider: new AuthorizationCode(
                redirectUri: ConfigHelper::get(key: 'app.url') . '/auth/callback'
            ),
            request: $request,
            route: $route
        );
    }

    /**
     * Redirect to login portal.
     */
    public function request(): RedirectResponse
    {
        $startData = $this->client->start();

        SessionHelper::set(
            name: 'cq_oauth_state',
            data: $startData->state
        );

        return Respond::redirect(
            url: $startData->uri
        );
    }

    /**
     * Callback for OAuth.
     */
    public function callback(): RedirectResponse|JsonResponse
    {
        try {
            $tokens = $this->client->callback(
                queryParams: $this->request->getQueryParams(),
                storedVar: SessionHelper::get('cq_oauth_state')
            );

            $user = $this->client->getUser(
                accessToken: $tokens->getAccessToken()
            );
        } catch (\Throwable $th) {
            if (AppHelper::isDebug()) {
                return Respond::json(
                    data: $th->getMessage()
                );
            }

            return Respond::redirect(
                url: '/?msg=error'
            );
        }

        if (!$user->isAllowed()) {
            if (!$user->isEmailVerified()) {
                return Respond::redirect(
                    url: '/?msg=not_verified'
                );
            }

            return Respond::redirect(
                url: '/?msg=not_registered'
            );
        }


        return Respond::redirect(
            url: AuthHelper::login(user: $user)
        );
    }
}
