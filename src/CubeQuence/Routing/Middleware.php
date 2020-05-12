<?php

namespace CQ\Routing;

use CQ\Routing\Route;
use CQ\Routing\Router;

class Middleware
{
    public static $router;

    //  TODO: Middleware::create(['prefix' => '/template', 'middleware' => [JSONMiddleware::class, SessionMiddleware::class]]);

    public static function create($config)
    {
        $router = self::$router;

        $router->group($config, function (Router $router) {
            Route::$router = $router;

            Route::get('/', 'GeneralController@index');
            Route::get('/', 'GeneralController@index');
            Route::get('/', 'GeneralController@index');
        });
    }
}
