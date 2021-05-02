<?php

declare(strict_types=1);

namespace CQ\Routing;

use CQ\Helpers\AppHelper;
use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Router as RouterBase;

final class Router
{
    private RouterBase $router;

    /**
     * Create router instance
     */
    public function __construct()
    {
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
                Respond::twig(
                    view: 'errors/404.twig',
                    code: 404
                )
            );
        } catch (\Throwable $e) {
            if (AppHelper::isDebug()) {
                throw $e;
            }

            $this->router->getPublisher()->publish(
                Respond::twig(
                    view: 'errors/500.twig',
                    code: 500
                )
            );
        }
    }
}
