<?php

namespace CQ\Config;

use CQ\Helpers\App;
use CQ\Helpers\Arr;

class Config
{
    /**
     * Define project dir.
     */
    public function __construct()
    {
        new Env(
            path: App::getRootPath() . '/'
        );

        $GLOBALS['cq_config'] = [];

        $config_dir = App::getRootPath() . '/config';
        $config_files = scandir(
            directory: App::getRootPath() . '/config'
        );

        unset($config_files[0]); // Removes . entry
        unset($config_files[1]); // Removes .. entry

        foreach ($config_files as $config_file) {
            $name = str_replace(
                search: '.php',
                replace: null,
                subject: $config_file
            );

            $this->attach(
                config_dir: $config_dir,
                name: $name
            );
        }
    }

    /**
     * Add config file.
     *
     * @param string $config_dir
     * @param string $name
     */
    private function attach(string $config_dir, string $name) : void
    {
        $data = require "{$config_dir}/{$name}.php";

        $GLOBALS['cq_config'][$name] = $data;
    }

    /**
     * Get config entry.
     *
     * @param string $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public static function get(string $key, $fallback = null) : mixed
    {
        $value = Arr::get(
            array: $GLOBALS['cq_config'],
            key: $key,
            default: $fallback
        );

        if ('true' === $value || 'false' === $value) {
            return 'true' === $value ? true : false;
        }

        return $value;
    }
}
