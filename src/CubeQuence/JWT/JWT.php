<?php

namespace CQ\JWT;

use Exception;
use Firebase\JWT\ExpiredException as FireBaseJWTExpiredException;
use Firebase\JWT\JWT as FireBaseJWT;

class JWT
{
    private $iss;
    private $aud;
    private $algorithm;
    private $private_key;
    private $public_key;

    /**
     * Define client variables.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->iss = $data['iss'];
        $this->aud = $data['aud'];
        $this->algorithm = 'RS256';
        $this->private_key = $data['private_key'];
        $this->public_key = $data['public_key'];
    }

    /**
     * Create JWT.
     *
     * @param array  $data
     * @param int    $seconds_valid
     * @param string $aud           optional
     *
     * @return string
     */
    public function create($data, $seconds_valid, $aud = null)
    {
        $head = [
            'iss' => $this->iss,
            'aud' => $aud ?: $this->aud,
            'iat' => time(),
            'exp' => time() + $seconds_valid,
        ];

        $payload = array_merge($head, $data);

        return FireBaseJWT::encode(
            $payload,
            $this->private_key,
            $this->algorithm
        );
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
        if (!$token) {
            throw new Exception('Token not provided');
        }

        try {
            $claims = FireBaseJWT::decode(
                $token,
                $this->public_key,
                [$this->algorithm]
            );
        } catch (FireBaseJWTExpiredException $e) {
            throw new Exception('Token has expired');
        } catch (Exception $e) {
            throw new Exception('Token is invalid');
        }

        if ($this->iss !== $claims->iss) {
            throw new Exception('Token iss not valid');
        }

        $intended_aud = $intended_aud ?: $this->aud;
        if ($intended_aud !== $claims->aud) {
            throw new Exception('Token aud not valid');
        }

        return $claims;
    }
}
