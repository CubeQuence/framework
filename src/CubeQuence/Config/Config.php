<?php

declare(strict_types=1);

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
     * Get config entry.
     *
     * @param mixed $fallback
     */
    public static function get(string $key, $fallback = null): mixed
    {
        $value = Arr::get(
            array: $GLOBALS['cq_config'],
            key: $key,
            default: $fallback
        );

        if ($value === 'true' || $value === 'false') {
            return $value === 'true';
        }

        return $value;
    }

    /**
     * Add config file.
     */
    private function attach(string $config_dir, string $name): void
    {
        $data = require "{$config_dir}/{$name}.php";

        $GLOBALS['cq_config'][$name] = $data;
    }
}
