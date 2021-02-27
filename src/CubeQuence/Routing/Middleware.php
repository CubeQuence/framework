<?php

declare(strict_types=1);

namespace CQ\Routing;

use Closure;
use MiladRahimi\PhpRouter\Router as RouterBase;

class Middleware
{
    /**
     * Instantiate class
     *
     * @param RouterBase $router
     */
    public function __construct(
        public RouterBase $router,
    ) {
    }

    /**
     * Create middleware handler
     *
     * @param array $config
     */
    public function create(array $config, Closure $routes): void
    {
        $this->router->group(
            attributes: $config,
            body: $routes
        );
    }
}
