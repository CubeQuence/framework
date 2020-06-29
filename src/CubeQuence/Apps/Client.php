<?php

namespace CQ\Apps;

use CQ\JWT\JWT;
use Exception;

class Client
{
    private $provider_url;
    private $app_id;
    private $app_url;
    private $debug;

    /**
     * Define client variables.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->provider_url = 'https://apps.lucacastelnuovo.nl';
        $this->app_id = $data['app_id'];
        $this->app_url = $data['app_url'];
        $this->debug = $data['debug'] ?: false;
    }

    /**
     * Generate authorization url.
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return "{$this->provider_url}/launch/{$this->app_id}";
    }

    /**
     * Validate code and return data.
     *
     * @param string $code
     * @param string $user_ip    optional
     * @param string $user_agent optional
     *
     * @throws Exception
     *
     * @return array
     */
    public function getData($code, $user_ip = null, $user_agent = null)
    {
        if (!$code) {
            throw new Exception('Token not provided');
        }

        $config = $this->getConfig();

        $provider = new JWT([
            'iss' => $this->provider_url,
            'aud' => $this->app_url,
            'public_key' => $config->public_key,
        ]);

        $claims = $provider->valid($code);

        if ($this->debug) {
            return $claims;
        }

        if ('auth' !== $claims->type) {
            throw new Exception('Token type not valid');
        }

        if ($user_ip && $claims->user_ip !== $user_ip) {
            throw new Exception('Authorized IP is different from current IP');
        }

        if ($user_agent && $claims->user_agent !== $user_agent) {
            throw new Exception('Authorized UserAgent is different from current UserAgent');
        }

        return $claims;
    }

    /**
     * Get jwt config from provider.
     *
     * @return object
     */
    public function getConfig()
    {
        $data = file_get_contents("{$this->provider_url}/jwt");

        return json_decode($data)->data;
    }
}
