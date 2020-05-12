<?php

namespace CQ\Config;

use CQ\Config\Env;
use CQ\Helpers\Arr;

class Config
{
    private $config = [];

    public function __construct()
    {
        new Env(__DIR__ . '/..');
    }

    public function attach($name)
    {
        $data = require __DIR__ . "/{$name}.php";

        var_dump($data);
        exit;

        $this->config = array_merge($this->config, $data);
    }

    public function get($key, $fallback = null)
    {
        return Arr::get($this->config, $key, $fallback);
    }
}
