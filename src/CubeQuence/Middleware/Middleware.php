<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Response\Respond;
use CQ\Response\Json;
use CQ\Response\NoContent;
use CQ\Response\Redirect;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware
{
    protected ServerRequestInterface $request;
    protected Route $route;
    protected Respond $respond;

    /**
     * Interface for middleware classes to tie into
     */
    abstract public function handleChild(Closure $next): Closure | Json | NoContent | Redirect;

    /**
     * Execute middleware
     */
    public function handle(
        ServerRequestInterface $request,
        Route $route,
        Closure $next
    ): Closure {
        $this->request = $request;
        $this->route = $route;
        $this->respond = new Respond();

        return $this->handleChild(next: $next);
    }
}
