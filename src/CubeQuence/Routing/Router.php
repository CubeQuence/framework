<?php

declare(strict_types=1);

namespace CQ\Routing;

use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Router as RouterBase;

final class Router
{
    private RouterBase $router;

    /**
     * Create router instance
     */
    public function __construct(
        public string $route_404 = '/',
        public string $route_500 = '/'
    ) {
        $this->router = RouterBase::create();
    }

    /**
     * Return route instance
     */
    public function getRoute(): Route
    {
        return new Route(router: $this->router);
    }

    /**
     * Return middleware instance
     */
    public function getMiddleware(): Middleware
    {
        return new Middleware(router: $this->router);
    }

    /**
     * Start the router
     */
    public function start(): void
    {
        try {
            $this->router->dispatch();
        } catch (RouteNotFoundException) {
            $this->router->getPublisher()->publish(
                Respond::redirect(
                    url: $this->route_404,
                    code: 404
                )
            );
        } /*catch (\Throwable $e) { // TODO: if you enable this route the debug window doesn't work
            if (App::isDebug()) {
                return throw new \Exception($e);
            }

            $this->router->getPublisher()->publish(
                Respond::redirect(
                    url: $this->route_500,
                    code: 500
                )
            );
        }*/
    }
}
