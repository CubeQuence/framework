<?php

namespace CQ\Controllers;

use CQ\Config\Config;
use CQ\Helpers\App as AppHelper;
use CQ\Helpers\State;
use CQ\Helpers\Request;
use CQ\Helpers\Session;
use CQ\Helpers\Guzzle;
use Throwable;

class Auth extends Controller
{
    /**
     * Return config
     *
     * @return object
     */
    private static function getConfig()
    {
        return (object) [
            'clientId'              => Config::get('auth.id'),
            'clientSecret'          => Config::get('auth.secret'),

            'urlAuthorize'          => 'https://auth.castelnuovo.xyz/oauth2/authorize',
            'urlAuthorizeDevice'    => 'https://auth.castelnuovo.xyz/oauth2/device_authorize',
            'urlAccessToken'        => 'https://auth.castelnuovo.xyz/oauth2/token',
            'urlUserDetails'        => 'https://auth.castelnuovo.xyz/oauth2/userinfo',

            'urlRedirect'           => urlencode(Config::get('app.url') . '/auth/callback'),
            'genQrCode'             => 'https://api.castelnuovo.xyz/qr?data=',
        ];
    }

    /**
     * Redirect to login portal.
     *
     * @return Redirect
     */
    public function request()
    {
        $config = self::getConfig();
        $state = State::set();

        $authRequest = "{$config->urlAuthorize}";
        $authRequest .= "?client_id={$config->clientId}";
        $authRequest .= "&response_type=code&approval_prompt=auto";
        $authRequest .= "&redirect_uri={$config->urlRedirect}";
        $authRequest .= "&state={$state}";

        return $this->redirect($authRequest);
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
            return $this->destroy('state');
        }

        try {
            $config = self::getConfig();

            $authorization = Guzzle::request('POST', $config->urlAccessToken, [
                'query' => [
                    'client_id' => $config->clientId,
                    'client_secret' => $config->clientSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => urldecode($config->urlRedirect),
                ],
            ])->data;

            $user = Guzzle::request('GET', $config->urlUserDetails, [
                'headers' => [
                    'Authorization' => "Bearer {$authorization->access_token}",
                ],
            ])->data;

            $expires_at = time() + $authorization->expires_in;
        } catch (\Throwable $th) {
            if (AppHelper::debug()) {
                return $this->respondJson($th->getMessage(), [], 400);
            }

            return $this->destroy('code');
        }

        if (!$user->roles) {
            return $this->destroy('not_registered');
        }

        $url = $this->login($user, $expires_at);

        return $this->redirect($url);
    }

    /**
    * Initiate device flow.
    *
    * @return Html
    */
    public function requestDevice()
    {
        $config = self::getConfig();

        $authRequest = Guzzle::request('POST', $config->urlAuthorizeDevice, [
            'query' => [
                'client_id' =>$config->clientId,
            ],
        ])->data;

        Session::set('device_code', $authRequest->device_code);

        return $this->respond('partials/device.twig', [
            'qr' => $config->genQrCode . urlencode($authRequest->verification_uri_complete),
        ]);
    }

    /**
     * Callback for OAuth device flow.
     *
     * @return Json
     */
    public function callbackDevice()
    {
        $config = self::getConfig();

        try {
            $authorization = Guzzle::request('POST', $config->urlAccessToken, [
                'query' => [
                    'client_id' => $config->clientId,
                    'client_secret' => $config->clientSecret,
                    'device_code' => Session::get('device_code'),
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                ],
            ])->data;

            $user = Guzzle::request('GET', $config->urlUserDetails, [
                'headers' => [
                    'Authorization' => "Bearer {$authorization->access_token}",
                ],
            ])->data;

            $expires_at = time() + $authorization->expires_in;
        } catch (Throwable $th) {
            $error = json_decode($th->getMessage())->error;

            switch ($error) {
                case 'authorization_pending':
                    return $this->respondJson('', []);

                case 'expired_token':
                    Session::destroy();
                    return $this->respondJson('The request has expired', [], 400);

                default:
                    Session::destroy();
                    return $this->respondJson('Invalid Request', [], 400);
            }
        }

        if (!$user->roles) {
            Session::destroy();

            return $this->respondJson('Please register for this application!', [], 400);
        }

        $url = $this->login($user, $expires_at);

        return $this->respondJson('You are logged in!', [
            'redirect' => $url,
        ]);
    }

    /**
     * Create session.
     *
     * @param object $user
     * @param string $expires_at
     *
     * @return string
     */
    public function login($user, $expires_at)
    {
        $return_to = Session::get('return_to');

        Session::destroy();

        // User Info
        Session::set('user', [
            'id' => $user->sub,
            'roles' => $user->roles,
            'email' => $user->email,
            'name' => $user->preferred_username,
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
            return $return_to;
        }

        return '/dashboard';
    }

    /**
     * Destroy session.
     *
     * @param string $msg
     *
     * @return Redirect
     */
    public function destroy($msg)
    {
        Session::destroy();

        return $this->redirect("/?msg={$msg}");
    }

    /**
     * Logout session.
     *
     * @return Redirect
     */
    public function logout()
    {
        Session::destroy();

        return $this->redirect('https://auth.castelnuovo.xyz/oauth2/logout?client_id=' . Config::get('auth.id'));
    }
}
