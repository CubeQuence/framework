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
    public function __construct(string $path)
    {
        $dotenv = Dotenv::createImmutable(paths: $path);
        $dotenv->load();
    }
}
