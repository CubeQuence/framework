<?php

declare(strict_types=1);

namespace CQ\Config;

use Dotenv\Dotenv;

class Env
{
    /**
     * Create dotenv instance.
     */
    public function __construct(string $path)
    {
        $dotenv = Dotenv::createImmutable(paths: $path);
        $dotenv->load();
    }
}
