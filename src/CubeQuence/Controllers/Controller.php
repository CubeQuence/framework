<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Response\Respond;
use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    protected Respond $respond;

    /**
     * Provide access for child classes.
     */
    public function __construct(
        protected ServerRequestInterface $request,
        protected Route $route
    ) {
        $this->respond = new Respond();
    }
}
