<?php

namespace CQ\Routing;

class Route
{
    public static $router;

    /**
     * Define GET route
     *
     * @param string $location
     * @param string $controller
     * 
     * @return void
     */
    public static function get($location, $controller)
    {
        self::$router->get($location, $controller);
    }
}
