<?php

namespace CQ\Helpers;

use CQ\Config\Config;

class App
{
    /**
     * Check apps envoirement.
     *
     * @param string|array $check optional
     *
     * @return string|bool
     */
    public static function environment($check = null)
    {
        $env = Config::get('app.env', 'production');
        $type = gettype($check);

        switch ($type) {
            case 'NULL':
                return $env;

            case 'string':
                return $env === $check;

            case 'array':
                return in_array($check, $env);

            default:
                throw new \Throwable('invalid variable type');
        }
    }

    /**
     * Return if debug is enabled.
     *
     * @return bool
     */
    public static function debug()
    {
        return Config::get('app.debug', false);
    }
}
