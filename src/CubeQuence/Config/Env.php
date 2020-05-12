<?php

namespace CQ\Config;

use Dotenv\Dotenv;

class Env
{
    /**
     * Create dotenv instance
     *
     * @param string $path
     * 
     * @return void
     */
    public function __construct($path)
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }
}
