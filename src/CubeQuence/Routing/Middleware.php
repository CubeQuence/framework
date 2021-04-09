<?php

declare(strict_types=1);

namespace CQ\Routing;

use Closure;
use MiladRahimi\PhpRouter\Router as RouterBase;

final class Middleware
{
    /**
     * Instantiate class
     */
    public function __construct(
        public RouterBase $router,
    ) {
    }

    /**
     * Create middleware handler
     */
    public function create(
        array $config,
        Closure $routes
    ): void {
        $this->router->group(
            attributes: $config,
            body: $routes
        );
    }
}
