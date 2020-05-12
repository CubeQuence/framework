<?php

namespace CQ\JWT;

use phpseclib\Crypt\RSA;

class Utils
{
    /**
     * Generate RSA keypair
     *
     * @param string $bits
     * 
     * @return array
     */
    public static function generateKeys($bits = 2048)
    {
        $rsa = new RSA();
        $keypair = $rsa->createKey(intval($bits));

        return $keypair;
    }
}
