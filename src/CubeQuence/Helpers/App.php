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
     * @throws \Exception
     */
    public static function environment(string|array|null $check = null) : string|bool// TODO: change to isEnvoirement
    {
        $env = Config::get(
            key: 'app.env',
            fallback: 'production'
        );
        $type = gettype(var: $check);

        switch ($type) { // TODO: use match if better
            case 'NULL':
                return $env;

            case 'string':
                return $env === $check;

            case 'array':
                return in_array(
                    needle: $check,
                    haystack: $env
                );

            default:
                throw new \Exception(message: 'invalid variable type');
        }
    }

    /**
     * Return if debug is enabled.
     *
     * @return bool
     */
    public static function debug() : bool // TODO: change to isDebug
    {
        return Config::get(
            key: 'app.debug',
            fallback: false
        );
    }

    /**
     * Get project root string
     *
     * @return string
     */
    public static function getRootPath() : string
    {
        list($path) = get_included_files();

        $path = dirname(path: $path);
        $path = str_replace(
            search: "/public",
            replace: null,
            subject: $path
        );

        return $path;
    }
}
