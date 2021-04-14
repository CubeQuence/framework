<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\RequestHelper;
use CQ\Response\JsonResponse;
use CQ\Response\NoContentResponse;
use CQ\Response\RedirectResponse;
use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware
{
    protected ServerRequestInterface $request;
    protected Route $route;
    protected Respond $respond;
    protected RequestHelper $requestHelper;

    /**
     * Interface for middleware classes to tie into
     */
    abstract public function handleChild(Closure $next): Closure | JsonResponse | NoContentResponse | RedirectResponse;

    /**
     * Execute middleware
     */
    public function handle(
        ServerRequestInterface $request,
        Route $route,
        Closure $next
    ): Closure | JsonResponse | NoContentResponse | RedirectResponse {
        $this->request = $request;
        $this->route = $route;
        $this->respond = new Respond();

        $this->requestHelper = new RequestHelper(
            request: $request
        );

        return $this->handleChild(next: $next);
    }
}
