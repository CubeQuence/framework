<?php

namespace CQ\Routing;

class Route
{
    /**
     * Define GET route
     *
     * @param MiladRahimi\PhpRouter\Router $router
     * @param string $location
     * @param string $controller
     * 
     * @return void
     */
    public static function get($router, $location, $controller)
    {
        $router->get($location, $controller);
    }
}
