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
     * @param array $middleware optional
     * 
     * @return void
     */
    public static function get($location, $controller, $middleware = [])
    {
        self::$router->get($location, $controller, $middleware);
    }

    /**
     * Define POST route
     *
     * @param string $location
     * @param string $controller
     * @param array $middleware optional
     * 
     * @return void
     */
    public static function post($location, $controller, $middleware = [])
    {
        self::$router->post($location, $controller, $middleware);
    }
    
     /**
     * Define PUT route
     *
     * @param string $location
     * @param string $controller
     * 
     * @return void
     */
    public static function put($location, $controller, $middleware = [])
    {
        self::$router->put($location, $controller, $middleware);
    }

    /**
     * Define PATCH route
     *
     * @param string $location
     * @param string $controller
     * 
     * @return void
     */
    public static function patch($location, $controller, $middleware = [])
    {
        self::$router->patch($location, $controller, $middleware);
    }
    
    /**
     * Define OPTIONS route
     *
     * @param string $location
     * @param string $controller
     * 
     * @return void
     */
    public static function options($location, $controller, $middleware = [])
    {
        self::$router->map('OPTIONS', $location, $controller, $middleware);
    }

    /**
     * Define DELETE route
     *
     * @param string $location
     * @param string $controller
     * @param array $middleware optional
     * 
     * @return void
     */
    public static function delete($location, $controller, $middleware = [])
    {
        self::$router->delete($location, $controller, $middleware);
    }

    /**
     * Define ANY route
     *
     * @param string $location
     * @param string $controller
     * @param array $middleware optional
     * 
     * @return void
     */
    public static function any($location, $controller, $middleware = [])
    {
        self::$router->any($location, $controller, $middleware);
    }
}
