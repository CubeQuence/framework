<?php

namespace CQ\JWT;

use phpseclib\Crypt\RSA;

class Utils
{
    /**
     * Generate RSA keypair.
     *
     * @param string $bits
     *
     * @return array
     */
    public static function generateKeys($bits = 2048)
    {
        $rsa = new RSA();

        return $rsa->createKey(intval($bits));
    }
}
