<?php

declare(strict_types=1);

namespace CQ\Config;

use CQ\Helpers\AppHelper;
use CQ\Helpers\ArrHelper;
use Dotenv\Dotenv;

final class Config
{
    private static Config | null $instance = null;
    private static array $config = [];

    /**
     * Define project dir.
     */
    private function __construct()
    {
        $appRootPath = AppHelper::getRootPath();
        $configDir = $appRootPath . '/config';

        // Load .env
        $dotenv = Dotenv::createImmutable(
            paths: $appRootPath . '/'
        );
        $dotenv->load();

        // Get all config files
        $config_files = scandir(
            directory: $configDir
        );

        unset($config_files[0]); // Removes . entry
        unset($config_files[1]); // Removes .. entry

        foreach ($config_files as $config_file) {
            $name = str_replace(
                search: '.php',
                replace: '',
                subject: $config_file
            );

            $configData = require "{$configDir}/{$name}.php";

            self::$config[$name] = $configData;
        }
    }

    /**
     * Get access to the Config singleton
     */
    private static function getInstance() : Config
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get config entry.
     */
    public static function get(string $key, $fallback = null): mixed
    {
        $configSingleton = self::getInstance();
        $config = $configSingleton::$config;

        $value = ArrHelper::get(
            array: $config,
            key: $key,
            default: $fallback
        );

        // Convert string to boolean
        if ($value === 'true' || $value === 'false') {
            return $value === 'true';
        }

        return $value;
    }
}
