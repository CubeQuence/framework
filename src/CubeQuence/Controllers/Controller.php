<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Helpers\RequestHelper;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
    protected ServerRequestInterface $request;
    protected RequestHelper $requestHelper;
    protected Route $route;

    /**
     * Provide access for child classes.
     */
    public function __construct(
        ServerRequestInterface $request,
        Route $route,
    ) {
        $this->request = $request;
        $this->route = $route;

        $this->requestHelper = new RequestHelper(
            request: $request
        );
    }
}
