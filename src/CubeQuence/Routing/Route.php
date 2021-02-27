<?php

declare(strict_types=1);

namespace CQ\Routing;

use MiladRahimi\PhpRouter\Router as RouterBase;

class Route
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
     * Define GET route
     *
     * @param array $controller
     */
    public function get(string $location, array $controller): void
    {
        $this->router->get(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define POST route
     *
     * @param array $controller
     */
    public function post(string $location, array $controller): void
    {
        $this->router->post(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define PUT route
     *
     * @param array $controller
     */
    public function put(string $location, array $controller): void
    {
        $this->router->put(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define PATCH route
     *
     * @param array $controller
     */
    public function patch(string $location, array $controller): void
    {
        $this->router->patch(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define OPTIONS route
     *
     * @param array $controller
     */
    public function options(string $location, array $controller): void
    {
        $this->router->define(
            method: 'OPTIONS',
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define DELETE route
     *
     * @param array $controller
     */
    public function delete(string $location, array $controller): void
    {
        $this->router->delete(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define ANY route
     *
     * @param array $controller
     */
    public function any(string $location, array $controller): void
    {
        $this->router->any(
            path: $location,
            controller: $controller
        );
    }
}
