<?php

namespace CQ\Controllers;

use MiladRahimi\PhpRouter\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

use CQ\Response\Respond;

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
