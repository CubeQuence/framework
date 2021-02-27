<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Config\Config;
use CQ\Helpers\App as AppHelper;
use CQ\Helpers\Guzzle;
use CQ\Helpers\Request;
use CQ\Helpers\Session;
use CQ\Helpers\State;
use CQ\Response\Html;
use CQ\Response\Json;
use CQ\Response\Redirect;

class Auth extends Controller
{
    private object $config;

    // TODO: make functions for getUser();, getAccessToken(device or normal)

    /**
     * Auth provider config
     */
    public function __construct()
    {
        $this->config = (object) [ // TODO: update varnames to underscore_case and change authorize -> authorize_url
            'clientId' => Config::get(key: 'auth.id'),
            'clientSecret' => Config::get(key: 'auth.secret'),

            'authorize' => 'https://auth.castelnuovo.xyz/oauth2/authorize',
            'authDevice' => 'https://auth.castelnuovo.xyz/oauth2/device_authorize',
            'accessToken' => 'https://auth.castelnuovo.xyz/oauth2/token',
            'userDetails' => 'https://auth.castelnuovo.xyz/oauth2/userinfo',
            'logout' => 'https://auth.castelnuovo.xyz/oauth2/logout',

            'redirect' => Config::get(key: 'app.url') . '/auth/callback',
            'qrCode' => 'https://api.castelnuovo.xyz/qr?data=',
        ];
    }

    /**
     * Redirect to login portal.
     */
    public function request(): Redirect
    {
        $state = State::set();

        $auth_url = "{$this->config->authorize}";
        $auth_url .= "?client_id={$this->config->clientId}";
        $auth_url .= '&response_type=code&approval_prompt=auto';
        $auth_url .= '&redirect_uri=' . urlencode($this->config->redirect);
        $auth_url .= "&state={$state}";

        return $this->respond->redirect($auth_url);
    }

    /**
     * Callback for OAuth.
     */
    public function callback(): Redirect
    {
        $code = $this->request->getQueryParams()['code']; // TODO: maybe easier getQueryParam('code')
        $state = $this->request->getQueryParams()['state'];

        if (!State::valid(provided_state: $state)) {
            return $this->destroy(msg: 'state');
        }

        try {
            $authorization = Guzzle::request(
                method: 'POST',
                url: $this->config->accessToken,
                data: [
                    'query' => [
                        'client_id' => $this->config->clientId,
                        'client_secret' => $this->config->clientSecret,
                        'code' => $code,
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => $this->config->redirect,
                    ],
                ]
            )->data;

            $user = Guzzle::request(
                method: 'GET',
                url: $this->config->userDetails,
                data: [
                    'headers' => [
                        'Authorization' => "Bearer {$authorization->access_token}",
                    ],
                ]
            )->data;

            $expires_at = time() + $authorization->expires_in;
        } catch (\Throwable $th) {
            if (AppHelper::debug()) {
                return $this->respond->prettyJson(
                    message: $th->getMessage(),
                    code: 400
                );
            }

            return $this->destroy(msg: 'code');
        }

        if (!$user->roles) {
            return $this->destroy(msg: 'not_registered');
        }

        $url = $this->login(
            user: $user,
            expires_at: $expires_at
        );

        return $this->respond->redirect(url: $url);
    }

    /**
     * Initiate device flow.
     */
    public function requestDevice(): Html
    {
        $auth_request = Guzzle::request(
            method: 'POST',
            url: $this->config->authDevice,
            data: [
                'query' => [
                    'client_id' => $this->config->clientId,
                ],
            ]
        )->data;

        Session::set(name: 'device_code', data: $auth_request->device_code);

        return $this->respond->twig(
            view: 'partials/device.twig',
            parameters:[
                'qr' => $this->config->qrCode . urlencode($auth_request->verification_uri_complete),
            ]
        );
    }

    /**
     * Callback for OAuth device flow.
     */
    public function callbackDevice(): Json
    {
        try {
            $authorization = Guzzle::request(
                method: 'POST',
                url: $this->config->accessToken,
                data: [
                    'query' => [
                        'client_id' => $this->config->clientId,
                        'client_secret' => $this->config->clientSecret,
                        'device_code' => Session::get(name: 'device_code'),
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                    ],
                ]
            )->data;

            $user = Guzzle::request(
                method: 'GET',
                url: $this->config->userDetails,
                data: [
                    'headers' => [
                        'Authorization' => "Bearer {$authorization->access_token}",
                    ],
                ]
            )->data;

            $expires_at = time() + $authorization->expires_in;
        } catch (\Throwable $th) {
            $error = json_decode(json: $th->getMessage())->error;

            switch ($error) { // TODO: check if map syntax is better
                case 'authorization_pending':
                    return $this->respond->prettyJson(message: '');

                case 'expired_token':
                    Session::destroy();

                    return $this->respond->prettyJson(
                        message: 'The request has expired!',
                        code: 400
                    );

                default:
                    Session::destroy();

                    return $this->respond->prettyJson(
                        message: 'Invalid request!',
                        code: 400
                    );
            }
        }

        if (!$user->roles) {
            Session::destroy();

            return $this->respond->prettyJson(
                message: 'Please register for this application!',
                code: 400
            );
        }

        $url = $this->login(user: $user, expires_at: $expires_at);

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
                'id' => $user->sub,
                'roles' => $user->roles,
                'email' => $user->email,
                'name' => $user->preferred_username,
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

        return $this->respond->redirect(
            url: "{$this->config->logout}?client_id=" . Config::get('auth.id')
        );
    }
}
