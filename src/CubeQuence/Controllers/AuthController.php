<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Controllers\Controller;
use CQ\Helpers\AuthHelper;
use CQ\Helpers\ConfigHelper;
use CQ\OAuth\Client;
use CQ\OAuth\Flows\FlowProvider;
use CQ\Response\HtmlResponse;
use CQ\Response\JsonResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class AuthController extends Controller
{
    protected Client $client;

    public function __construct(
        FlowProvider $flowProvider,
        ServerRequestInterface $request,
        Route $route,
    ) {
        $this->client = new Client(
            flowProvider: $flowProvider,
            authorizationServer: ConfigHelper::get(key: 'auth.authorization_server'),
            clientId: ConfigHelper::get(key: 'auth.client_id'),
            clientSecret: ConfigHelper::get(key: 'auth.client_secret')
        );

        parent::__construct(
            request: $request,
            route: $route
        );
    }

    /**
     * Redirect or show login portal
     */
    abstract public function request(): RedirectResponse | HtmlResponse;

    /**
     * Callback for OAuth.
     */
    abstract public function callback(): RedirectResponse | JsonResponse;

    /**
     * Logout through oauth server
     */
    final public function logout(): RedirectResponse
    {
        AuthHelper::logout();

        return Respond::redirect(
            url: $this->client->logout()
        );
    }
}
