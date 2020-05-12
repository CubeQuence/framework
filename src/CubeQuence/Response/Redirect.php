<?php

namespace CQ\Response;

use Zend\Diactoros\Response\RedirectResponse;

class Redirect
{
    /**
     * Redirect Response
     *
     * @param string $to
     * @param string $code
     * 
     * @return RedirectResponse
     */
    public function __construct($to, $code)
    {
        return new RedirectResponse($to, $code);
    }
}
