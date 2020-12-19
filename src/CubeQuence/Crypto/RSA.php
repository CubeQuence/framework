<?php

namespace CQ\Crypto;

use CQ\Helpers\File;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA as RSALib;

class RSA
{
    private $private_key;
    public $public_key;

    /**
     * Get keys
     *
     * @param string $key_path
     * @param string $password optional
     *
     * @return void
     */
    public function __construct($key_path, $password = false)
    {
        $key_file = new File($key_path);

        $this->private_key = PublicKeyLoader::load($key_file->read(), $password);
        $this->public_key = $this->private_key->getPublicKey();
    }

    /**
     * Generate keypair
     *
     * @param int $bits optional
     *
     * @return array
     */
    public static function genKeys($bits = 2048)
    {
        $private_key = RSALib::createKey($bits);
        $public_key = $private_key->getPublicKey();

        return [
            'private_key' => $private_key,
            'public_keu' => $public_key,
        ];
    }

    /**
     * Sign message
     *
     * @param string $message
     *
     * @return array
     */
    public function sign($message)
    {
        $signature = $this->private_key->sign($message);

        return [
            'message' => $message,
            'signature' => $signature,
        ];
    }

    /**
     * Verify signature
     *
     * @param string $message
     * @param string $signature
     *
     * @return bool
     */
    public function verify($message, $signature)
    {
        return $this->public_key->verify($message, $signature);
    }

    /**
     * Encrypt
     *
     * @param string $string
     *
     * @return string
     */
    public function encrypt($string)
    {
        return $this->public_key->encrypt($string);
    }

    /**
     * Decrypt
     *
     * @param string $string
     *
     * @return string
     */
    public function decrypt($string)
    {
        return $this->private_key->decrypt($string);
    }
}
