<?php

declare(strict_types=1);

namespace CQ\Middleware;

use Closure;
use CQ\Helpers\RequestHelper;
use CQ\Response\HtmlResponse;
use CQ\Response\JsonResponse;
use CQ\Response\NoContentResponse;
use CQ\Response\RedirectResponse;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware
{
    protected ServerRequestInterface $request;
    protected RequestHelper $requestHelper;
    protected Route $route;

    /**
     * Interface for middleware classes to tie into
     */
    abstract public function handleChild(Closure $next): Closure | HtmlResponse | JsonResponse | NoContentResponse | RedirectResponse;

    /**
     * Execute middleware
     */
    public function handle(
        ServerRequestInterface $request,
        Route $route,
        Closure $next
    ): Closure | HtmlResponse | JsonResponse | NoContentResponse | RedirectResponse {
        $this->request = $request;
        $this->route = $route;

        $this->requestHelper = new RequestHelper(
            request: $request
        );

        return $this->handleChild(next: $next);
    }
}
