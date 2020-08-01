<?php

namespace CQ\Routing;

class Middleware
{
    public static $router;

    public static function create($config, $routes)
    {
        $router = self::$router;

        $router->group($config, $routes);
    }
}
