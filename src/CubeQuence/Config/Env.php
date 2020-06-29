<?php

namespace CQ\Config;

use Dotenv\Dotenv;

class Env
{
    /**
     * Create dotenv instance.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }
}
