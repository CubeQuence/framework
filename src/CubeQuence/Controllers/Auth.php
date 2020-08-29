<?php

namespace CQ\Controllers;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use CQ\Config\Config;
use CQ\Helpers\App as AppHelper;
use CQ\Helpers\State;
use CQ\Helpers\Request;
use CQ\Helpers\Session;

class Auth extends Controller
{
    private $provider;

    /**
     * Initialize the provider.
     */
    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId' => Config::get('auth.id'),
            'clientSecret' => Config::get('auth.secret'),
            'redirectUri' => Config::get('app.url') . '/auth/callback',
            'urlAuthorize' => 'https://auth.castelnuovo.xyz/oauth2/authorize',
            'urlAccessToken' => 'https://auth.castelnuovo.xyz/oauth2/token',
            'urlResourceOwnerDetails' => 'https://auth.castelnuovo.xyz/oauth2/userinfo',
        ]);
    }

    /**
     * Redirect to login portal.
     *
     * @return Redirect
     */
    public function request()
    {
        $authUrl = $this->provider->getAuthorizationUrl();
        State::set($this->provider->getState());

        return $this->redirect($authUrl);
    }

    /**
     * Callback for OAuth.
     *
     * @param object $request
     *
     * @return Redirect
     */
    public function callback($request)
    {
        $code = $request->getQueryParams()['code'];
        $state = $request->getQueryParams()['state'];

        if (!State::valid($state)) {
            return $this->logout('state');
        }

        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $expires_at = $accessToken->getExpires();
            $user = $this->provider->getResourceOwner($accessToken);
            $user = $user->toArray();
        } catch (IdentityProviderException $e) {
            if (AppHelper::debug()) {
                var_dump($e->getMessage());

                exit;
            }

            return $this->logout('code');
        }

        if ($accessToken->hasExpired()) {
            return $this->logout('code');
        }

        if (!$user['roles']) {
            return $this->logout('not_registered');
        }

        return $this->login($user, $expires_at);
    }

    /**
     * Create session.
     *
     * @param object $user
     * @param string $expires_at
     *
     * @return Redirect
     */
    public function login($user, $expires_at)
    {
        $return_to = Session::get('return_to');

        Session::destroy();

        // User Info
        Session::set('user', [
            'id' => $user['sub'],
            'roles' => $user['roles'],
            'email' => $user['email'],
            'name' => $user['preferred_username'],
        ]);

        // Auth Info
        Session::set('session', [
            'expires_at' => $expires_at,
            'created_at' => time(),
            'ip' => Request::ip(),
        ]);

        // Activity Info
        Session::set('last_activity', time());

        if ($return_to) {
            return $this->redirect($return_to);
        }

        return $this->redirect('/dashboard');
    }

    /**
     * Destroy session.
     *
     * @param string $msg optional
     *
     * @return Redirect
     */
    public function logout($msg = 'logout')
    {
        Session::destroy();

        if ($msg) {
            return $this->redirect("/?msg={$msg}");
        }

        return $this->redirect('/');
    }
}
