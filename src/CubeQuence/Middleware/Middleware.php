<?php

namespace CQ\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use MiladRahimi\PhpRouter\Routing\Route;

use CQ\Response\Respond;

abstract class Middleware
{
    protected ServerRequestInterface $request;
    protected Route $route;
    protected Respond $respond;

    /**
     * Interface for middleware classes to tie into
     *
     * @param Closure $next
     *
     * @return Closure
     */
    abstract public function handleChild(Closure $next);

    /**
     * Execute middleware
     *
     * @param ServerRequestInterface $request
     * @param Route $route
     * @param Closure $next
     *
     * @return Closure
     */
    public function handle(
        ServerRequestInterface $request,
        Route $route,
        Closure $next
    ) {
        $this->request = $request;
        $this->route = $route;
        $this->respond = new Respond();

        return $this->handleChild(next: $next);
    }
}
