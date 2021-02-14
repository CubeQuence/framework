<?php

namespace CQ\Controllers;

use CQ\Helpers\Auth;
use CQ\Response\Html;
use CQ\Controllers\Controller;

class General extends Controller
{
    /**
     * Index screen.
     *
     * @return Html
     */
    public function index() : Html
    {
        $msg = $this->request->getQueryParams()['msg'] ?? null;

        switch ($msg) { // TODO: replace with map
            case 'logout':
                $msg = 'You have been logged out!';
                break;

            case 'state':
                $msg = 'Please try again!';
                break;

            case 'code':
                $msg = 'Invalid authentication!';
                break;

            case 'not_registered':
                $msg = 'Please register for this application!';
                break;

            default:
                $msg = '';
                break;
        }

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
     *
     * @param string $code
     *
     * @return Html
     */
    public function error(string $code) : Html
    {
        switch ($code) { // TODO: replace met map
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
