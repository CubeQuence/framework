<?php

namespace CQ\Crypto;

use CQ\Config\Config;
use phpseclib3\Crypt\AES as AESLib;
use phpseclib3\Crypt\Random;
use Exception;

class AES
{
    /**
     * Get encryption key
     *
     * @param string $key optional
     *
     * @return string
     */
    private static function getKey($key = null)
    {
        $key = $key ?: Config::get('app.key');

        if (!$key) {
            throw new Exception('No key found!');
        }

        return $key;
    }

    /**
     * Encrypt string
     *
     * @param string $string
     * @param string $key optional
     *
     * @return string
     */
    public static function encrypt($string, $key = null)
    {
        $key = self::getKey($key);

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
     * @param string $enc_string
     * @param string $key optional
     *
     * @return string
     */
    public static function decrypt($enc_string, $key = null)
    {
        $key = self::getKey($key);

        list($decoded_enc_string, $iv) = explode('|', $enc_string);

        $cipher = new AESLib('ctr');
        $iv = base64_decode($iv);

        $cipher->setIV($iv);
        $cipher->setPassword($key);

        $decoded_enc_string = base64_decode($decoded_enc_string);

        return $cipher->decrypt($decoded_enc_string);
    }
}
