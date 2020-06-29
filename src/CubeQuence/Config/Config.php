<?php

namespace CQ\Config;

use CQ\Helpers\Arr;

class Config
{
    private $dir;

    /**
     * Define project dir.
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = "{$dir}/../";

        new Env($this->dir);

        $GLOBALS['cq_config'] = [];
    }

    /**
     * Add config file.
     *
     * @param string $name
     */
    public function attach($name)
    {
        $data = require $this->dir."config/{$name}.php";

        $GLOBALS['cq_config'][$name] = $data;
    }

    /**
     * Get config entry.
     *
     * @param string $key
     * @param mixed  $fallback
     *
     * @return mixed
     */
    public static function get($key, $fallback = null)
    {
        $value = Arr::get($GLOBALS['cq_config'], $key, $fallback);

        if ('true' === $value || 'false' === $value) {
            return 'true' === $value ? true : false;
        }

        return $value;
    }
}
