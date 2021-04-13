<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Config\Config;
use CQ\Helpers\SessionHelper;
use CQ\Response\Respond;
use CQ\OAuth\Client;
use CQ\OAuth\Exceptions\AuthException;
use CQ\OAuth\Flows\Provider\AuthorizationCode;
use CQ\OAuth\Flows\Provider\Device;
use CQ\OAuth\Models\User;
use CQ\Response\HtmlResponse;
use CQ\Response\JsonResponse;
use CQ\Response\RedirectResponse;

class AuthController extends Controller
{
    private Client $client;
    private Client $deviceClient;

    /**
     * Auth provider config
     */
    public function __construct()
    {
        $this->client = new Client(
            flowProvider: new AuthorizationCode(
                redirectUri: Config::get('app.url') . '/auth/callback',
            ),
            authorizationServer: 'https://auth.castelnuovo.xyz',
            clientId: Config::get(key: 'auth.id'),
            clientSecret: Config::get(key: 'auth.secret')
        );

        $this->deviceClient = new Client(
            flowProvider: new Device(),
            authorizationServer: 'https://auth.castelnuovo.xyz',
            clientId: Config::get(key: 'auth.id'),
            clientSecret: Config::get(key: 'auth.secret')
        );
    }

    /**
     * Redirect to login portal.
     */
    public function request(): RedirectResponse
    {
        $startData = $this->client->start();

        SessionHelper::set('state', $startData->state);

        return Respond::redirect($startData->uri);
    }

    /**
     * Callback for OAuth.
     */
    public function callback(): RedirectResponse
    {
        $queryParams = $this->request->getQueryParams();

        try {
            $tokens = $this->client->callback(
                queryParams: $queryParams,
                storedVar: SessionHelper::get('state')
            );

            $user = $this->client->getUser(
                accessToken: $tokens->access_token
            );
        } catch (AuthException) {
            return $this->destroy(msg: 'state');
        } catch (\Throwable) {
            return $this->destroy(msg: 'error');
        }

        if (! $user->allowed) {
            return $this->destroy(msg: 'not_allowed');
        }

        $url = $this->login(
            user: $user,
            expires_at: $tokens->expires_at
        );

        return Respond::redirect(url: $url);
    }

    /**
     * Initiate device flow.
     */
    public function requestDevice(): HtmlResponse
    {
        $startData = $this->deviceClient->start();

        SessionHelper::set(name: 'device_code', data: $startData->device_code);

        return Respond::twig(
            view: 'partials/device.twig',
            parameters:[
                'qr' => $startData->uri,
            ]
        );
    }

    /**
     * Callback for OAuth device flow.
     */
    public function callbackDevice(): JsonResponse
    {
        try {
            $tokens = $this->client->callback(
                queryParams: [],
                storedVar: SessionHelper::get('state')
            );

            $user = $this->client->getUser(
                accessToken: $tokens->access_token
            );
        } catch (AuthException $th) {
            if (! $th->getMessage()) {
                return Respond::prettyJson(message: '');
            }

            return Respond::prettyJson(
                message: $th->getMessage(),
                code: 400
            );
        } catch (\Throwable $th) {
            return Respond::prettyJson(
                message: 'Unknown error occured!',
                code: 400
            );
        }

        if (! $user->allowed) {
            SessionHelper::destroy();

            return Respond::prettyJson(
                message: 'Please register for this application!',
                code: 400
            );
        }

        $url = $this->login(
            user: $user,
            expires_at: $tokens->expires_at
        );

        return Respond::prettyJson(
            message: 'You are logged in!',
            data: [
                'redirect' => $url,
            ]
        );
    }

    /**
     * Create session.
     */
    public function login(User $user, string $expires_at): string
    {
        $return_to = SessionHelper::get(name: 'return_to');

        SessionHelper::destroy();

        // CQ\OAuth\Models\User
        SessionHelper::set(
            name: 'user',
            data: $user
        );

        // Auth Info
        SessionHelper::set(
            name: 'session',
            data: [
                'expires_at' => $expires_at,
                'created_at' => time(),
                'ip' => $this->requestHelper->ip(),
            ]
        );

        // Activity Info
        SessionHelper::set(name: 'last_activity', data: time());

        if ($return_to) {
            return $return_to;
        }

        return '/dashboard';
    }

    /**
     * Destroy session.
     */
    public function destroy(string $msg): RedirectResponse
    {
        SessionHelper::destroy();

        return Respond::redirect(url: "/?msg={$msg}");
    }

    /**
     * Logout session.
     */
    public function logout(): RedirectResponse
    {
        SessionHelper::destroy();

        $logoutUrl = $this->client->logout();

        return Respond::redirect(url: $logoutUrl);
    }
}
