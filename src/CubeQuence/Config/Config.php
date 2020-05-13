<?php

namespace CQ\Config;

use CQ\Config\Env;
use CQ\Helpers\Arr;

class Config
{
    private $dir;
    private static $config = [];

    /**
     * Define project dir
     * 
     * @param string $dir
     * 
     * @return void
     */
    public function __construct($dir)
    {
        $this->dir = "{$dir}/../";

        new Env($this->dir);
    }

    /**
     * Add config file
     *
     * @param string $name
     * 
     * @return void
     */
    public function attach($name)
    {
        $data = require $this->dir . "config/{$name}.php";

        self::$config = array_merge(self::$config, [$name => $data]);
    }

    /**
     * Get config entry
     *
     * @param string $key
     * @param mixed $fallback
     * 
     * @return mixed
     */
    public static function get($key, $fallback = null)
    {
        $value = Arr::get(self::$config, $key, $fallback);

        if ($value === "true" || $value === "false") {
            return $value === "true" ? true : false;
        }

        return $value;
    }
}
