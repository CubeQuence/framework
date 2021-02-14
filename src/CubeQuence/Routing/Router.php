<?php

namespace CQ\Routing;

use MiladRahimi\PhpRouter\Router as RouterBase;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;

use CQ\Routing\Route;
use CQ\Response\Redirect;
use CQ\Routing\Middleware;

class Router
{
    private RouterBase $router;

    /**
     * Create router instance
     *
     * @param string $route_404 optional
     * @param string $route_500 optional
     *
     * @return void
     */
    public function __construct(
        public string $route_404 = '/',
        public string $route_500 = '/'
    ) {
        $this->router = RouterBase::create();
    }

    /**
     * Set container
     *
     * @param mixed $id TODO: set correct type
     * @param mixed $concrete
     *
     * @return void
     */
    public function setContainer($id, $concrete) : void
    {
        $this->router->getContainer()->singleton(
            id: $id,
            concrete: $concrete
        );
    }

    /**
     * Return route instance
     *
     * @return Route
     */
    public function getRoute() : Route
    {
        return new Route($this->router);
    }

    /**
     * Return middleware instance
     *
     * @return Middleware
     */
    public function getMiddleware() : Middleware
    {
        return new Middleware($this->router);
    }

    /**
     * Start the router
     *
     * @return void
     */
    public function start() : void
    {
        try {
            $this->router->dispatch();
        } catch (RouteNotFoundException) {
            $this->router->getPublisher()->publish(
                new Redirect(
                    url: $this->route_404,
                    code: 404,
                    headers: []
                )
            );
        } /*catch (\Throwable $e) { // TODO: if you enable this route the debug window doesn't work
            if (App::debug()) {
                return throw new \Exception($e);
            }

            $this->router->getPublisher()->publish(
                new Redirect($this->route_500, 500, [])
            );
        }*/
    }
}
