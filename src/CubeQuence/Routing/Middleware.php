<?php

namespace CQ\Routing;

class Middleware
{
    public static \MiladRahimi\PhpRouter\Router $router;

    public static function create($config, $routes)
    {
        $router = self::$router;

        $router->group($config, $routes);
    }
}
