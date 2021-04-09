<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Config\Config;
use CQ\Helpers\Request;
use CQ\Helpers\Session;
use CQ\Response\Html;
use CQ\Response\Json;
use CQ\Response\Redirect;
use OAuth\Client; // TODO: replace with CQ\OAuth\Client
use OAuth\Exceptions\AuthException;
use OAuth\Flows\Provider\AuthorizationCode;
use OAuth\Flows\Provider\Device;

class Auth extends Controller
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
                redirectUri: ''
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
    public function request(): Redirect
    {
        $startData = $this->client->start();

        Session::set('state', $startData->state);

        return $this->respond->redirect($startData->uri);
    }

    /**
     * Callback for OAuth.
     */
    public function callback(): Redirect
    {
        $queryParams = $this->request->getQueryParams();

        try {
            $tokens = $this->client->callback(
                queryParams: $queryParams,
                storedVar: Session::get('state')
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

        return $this->respond->redirect(url: $url);
    }

    /**
     * Initiate device flow.
     */
    public function requestDevice(): Html
    {
        $startData = $this->deviceClient->start();

        Session::set(name: 'device_code', data: $startData->device_code);

        return $this->respond->twig(
            view: 'partials/device.twig',
            parameters:[
                'qr' => $startData->uri,
            ]
        );
    }

    /**
     * Callback for OAuth device flow.
     */
    public function callbackDevice(): Json
    {
        try {
            $tokens = $this->client->callback(
                queryParams: [],
                storedVar: Session::get('state')
            );

            $user = $this->client->getUser(
                accessToken: $tokens->access_token
            );
        } catch (AuthException $th) {
            if (! $th->getMessage()) {
                return $this->respond->prettyJson(message: '');
            }

            return $this->respond->prettyJson(
                message: $th->getMessage(),
                code: 400
            );
        } catch (\Throwable $th) {
            return $this->respond->prettyJson(
                message: 'Unknown error occured!',
                code: 400
            );
        }

        if (! $user->allowed) {
            Session::destroy();

            return $this->respond->prettyJson(
                message: 'Please register for this application!',
                code: 400
            );
        }

        $url = $this->login(
            user: $user,
            expires_at: $tokens->expires_at
        );

        return $this->respond->prettyJson(
            message: 'You are logged in!',
            data: [
                'redirect' => $url,
            ]
        );
    }

    /**
     * Create session.
     */
    public function login(object $user, string $expires_at): string
    {
        $return_to = Session::get(name: 'return_to');

        Session::destroy();

        // User Info
        Session::set(
            name: 'user',
            data: [
                'id' => $user->id,
                'email' => $user->email,
                'roles' => $user->roles,
            ]
        );

        // Auth Info
        Session::set(
            name: 'session',
            data: [
                'expires_at' => $expires_at,
                'created_at' => time(),
                'ip' => Request::ip(),
            ]
        );

        // Activity Info
        Session::set(name: 'last_activity', data: time());

        if ($return_to) {
            return $return_to;
        }

        return '/dashboard';
    }

    /**
     * Destroy session.
     */
    public function destroy(string $msg): Redirect
    {
        Session::destroy();

        return $this->respond->redirect(url: "/?msg={$msg}");
    }

    /**
     * Logout session.
     */
    public function logout(): Redirect
    {
        Session::destroy();

        $logoutUrl = $this->client->logout();

        return $this->respond->redirect(url: $logoutUrl);
    }
}
