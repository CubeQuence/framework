<?php

namespace CQ\Controllers;

use CQ\Config\Config;
use CQ\Helpers\App;
use CQ\Response\Html;
use CQ\Response\Json;
use CQ\Response\Redirect;
use CQ\Response\Twig;

class Controller
{
    // private $request;
    private $twig;

    /**
     * Provide access for child classes.
     */
    public function __construct()
    {
        $twig = new Twig(Config::get('cache.views') && !App::debug());
        $this->twig = $twig->get();
        $this->twig->addGlobal('app', Config::get('app'));
        $this->twig->addGlobal('analytics', Config::get('analytics'));
    }

    /**
     * Shorthand redirect function.
     *
     * @param string $to
     * @param int    $code optional
     *
     * @return Redirect
     */
    protected function redirect($to, $code = 302)
    {
        return new Redirect($to, $code);
    }

    /**
     * Shorthand HTML response function.
     *
     * @param string $view
     * @param array  $parameters
     * @param int    $code       optional
     *
     * @return Html
     */
    protected function respond($view, $parameters = [], $code = 200)
    {
        return new Html(
            $this->twig->render(
                $view,
                $parameters
            ),
            $code
        );
    }

    /**
     * Shorthand JSON response function.
     *
     * @param string $message
     * @param array  $data    optional
     * @param int    $code    optional
     *
     * @return JsonResponse
     */
    protected function respondJson($message, $data = [], $code = 200)
    {
        return new Json([
            'success' => 200 === $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
