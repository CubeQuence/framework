<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Helpers\AuthHelper;
use CQ\Response\HtmlResponse;
use CQ\Response\Respond;

class General extends Controller
{
    /**
     * Index screen.
     */
    public function index(): HtmlResponse
    {
        $msg = match ($this->requestHelper->getQueryParam('msg')) {
            'state' => 'Please try again!',
            'error' => 'Unknown error occured!',
            'logout' => 'You have been logged out!',
            'not_allowed' => 'Please register or contact the administrator!',
            default => ''
        };

        return Respond::twig(
            view: 'index.twig',
            parameters: [
                'message' => $msg,
                'logged_in' => AuthHelper::valid(),
            ]
        );
    }

    /**
     * Error screen.
     */
    public function error(string $code): HtmlResponse
    {
        $error = match($code) {
            '403' => 'Oops! Access denied',
            '404' => 'Oops! Page not found',
            '500' => 'Oops! Server error',

            default => 'Oops! Unknown Error'
        };

        $description = match($code) {
            '403' => 'Access to this page is forbidden',
            '404' => 'The page you requested could not be found',
            '500' => 'We are experiencing some technical issues',
            // no break
            default => 'Unknown error occured'
        };

        return Respond::twig(
            view: 'error.twig',
            parameters: [
                'code' => $code,
                'error' => $error,
                'description' => $description,
            ],
            code: (int) $code
        );
    }
}
