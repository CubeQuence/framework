<?php

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
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function get(string $location, array $controller) : void
    {
        $this->router->get(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define POST route
     *
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function post(string $location, array $controller) : void
    {
        $this->router->post(
            path: $location,
            controller: $controller
        );
    }

    /**
    * Define PUT route
    *
    * @param string $location
    * @param array $controller
    *
    * @return void
    */
    public function put(string $location, array $controller) : void
    {
        $this->router->put(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define PATCH route
     *
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function patch(string $location, array $controller) : void
    {
        $this->router->patch(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define OPTIONS route
     *
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function options(string $location, array $controller) : void
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
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function delete(string $location, array $controller) : void
    {
        $this->router->delete(
            path: $location,
            controller: $controller
        );
    }

    /**
     * Define ANY route
     *
     * @param string $location
     * @param array $controller
     *
     * @return void
     */
    public function any(string $location, array $controller) : void
    {
        $this->router->any(
            path: $location,
            controller: $controller
        );
    }
}
