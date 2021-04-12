<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Helpers\RequestHelper;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
    protected RequestHelper $requestHelper;

    /**
     * Provide access for child classes.
     */
    public function __construct(
        protected ServerRequestInterface $request,
        protected Route $route,
    ) {
        $this->requestHelper = new RequestHelper(
            request: $request
        );
    }
}
