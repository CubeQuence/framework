<?php

use CQ\JWT\JWT as CQjwt;

class JWT
{
    private $jwt;

    /**
     * Create jwt instance
     *
     * @param array $data
     * 
     * @return void
     */
    public function __construct($data)
    {
        $this->jwt = new CQjwt($data);
    }

    /**
     * Generate RSA keypair
     *
     * @param string $bits
     * 
     * @return array
     */
    public static function generateKeys($bits = 2048)
    {
        return CQjwt::generateKeys($bits);
    }

    /**
     * Create JWT.
     *
     * @param array  $data
     * @param int $seconds_valid
     * @param string $aud optional
     *
     * @return string
     */
    public function create($data, $seconds_valid, $aud = null)
    {
        return $this->jwt->create($data, $seconds_valid, $aud = null);
    }

    /**
     * Decode and validate JWT.
     *
     * @param string $token
     * @param string $intended_aud optional
     *
     * @return array
     */
    public function valid($token, $intended_aud = null)
    {
        return $this->jwt->valid($token, $intended_aud);
    }
}
