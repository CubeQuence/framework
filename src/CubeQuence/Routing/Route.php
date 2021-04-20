<?php

declare(strict_types=1);

namespace CQ\Routing;

use MiladRahimi\PhpRouter\Router as RouterBase;

final class Route
{
    /**
     * Instantiate class
     */
    public function __construct(
        public RouterBase $router,
    ) {
    }

    /**
     * Define GET route
     */
    public function get(
        string $location,
        array $controller
    ): void {
        $this->router->get(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define POST route
     */
    public function post(
        string $location,
        array $controller
    ): void {
        $this->router->post(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define PUT route
     */
    public function put(
        string $location,
        array $controller
    ): void {
        $this->router->put(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define PATCH route
     */
    public function patch(
        string $location,
        array $controller
    ): void {
        $this->router->patch(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define OPTIONS route
     */
    public function options(
        string $location,
        array $controller
    ): void {
        $this->router->define(
            method: 'OPTIONS',
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define DELETE route
     */
    public function delete(
        string $location,
        array $controller
    ): void {
        $this->router->delete(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define ANY route
     */
    public function any(
        string $location,
        array $controller
    ): void {
        $this->router->any(
            path: $location,
            controller: $controller
        );
    }
}
