<?php

declare(strict_types=1);

namespace CQ\Controllers;

use CQ\Helpers\Auth;
use CQ\Response\Html;

class General extends Controller
{
    /**
     * Index screen.
     */
    public function index(): Html
    {
        $msg = $this->request->getQueryParams()['msg'] ?? null;

        $msg = match ($msg) {
            'state' => 'Please try again!',
            'error' => 'Unknown error occured!',
            'logout' => 'You have been logged out!',
            'not_allowed' => 'Please register for this application!',
            default => ''
        };

        return $this->respond->twig(
            view: 'index.twig',
            parameters: [
                'message' => $msg,
                'logged_in' => Auth::valid(),
            ]
        );
    }

    /**
     * Error screen.
     */
    public function error(string $code): Html
    {
        switch ($code) {
            case '403':
                $short_message = 'Oops! Access denied';
                $message = 'Access to this page is forbidden';
                break;
            case '404':
                $short_message = 'Oops! Page not found';
                $message = 'We are sorry, but the page you requested was not found';
                break;
            case '500':
                $short_message = 'Oops! Server error';
                $message = 'We are experiencing some technical issues';
                break;

            default:
                $short_message = 'Oops! Unknown Error';
                $message = 'Unknown error occured';
                $code = 400;
                break;
        }

        return $this->respond->twig(
            view: 'error.twig',
            parameters: [
                'code' => $code,
                'short_message' => $short_message,
                'message' => $message,
            ],
            code: $code
        );
    }
}
