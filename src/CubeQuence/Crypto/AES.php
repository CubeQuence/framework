<?php

namespace CQ\Crypto;

use phpseclib3\Crypt\AES as AESLib;
use phpseclib3\Crypt\Random;

class AES
{
    /**
     * Encrypt string
     *
     * @param string $key
     * @param string $string
     *
     * @return string
     */
    public static function encrypt($key, $string)
    {
        $cipher = new AESLib('ctr');
        $iv = Random::string(16);

        $cipher->setIV($iv);
        $cipher->setPassword($key);

        $enc_string = $cipher->encrypt($string);

        return base64_encode($enc_string) . '|' . base64_encode($iv);
    }

    /**
     * Decrypt string
     *
     * @param string $key
     * @param string $enc_string
     *
     * @return string
     */
    public static function decrypt($key, $enc_string)
    {
        list($decoded_enc_string, $iv) = explode('|', $enc_string);

        $cipher = new AESLib('ctr');
        $iv = base64_decode($iv);

        $cipher->setIV($iv);
        $cipher->setPassword($key);

        $decoded_enc_string = base64_decode($decoded_enc_string);

        return $cipher->decrypt($decoded_enc_string);
    }
}
