<?php

namespace CQ\Response;

use CQ\Helpers\Request;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class Twig
{
    private $twig;

    /**
     * Create twig instance.
     *
     * @param bool $cache
     */
    public function __construct($cache = true)
    {
        $loader = new FilesystemLoader('../views');
        if ($cache) {
            $this->twig = new Environment($loader, ['cache' => '../storage/views']);
        } else {
            $this->twig = new Environment($loader);
        }
    }

    /**
     * Return twig instance.
     *
     * @return Environment
     */
    public function get()
    {
        return $this->twig;
    }

    /**
     * Render template in string form.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return string
     */
    public static function renderFromText($template, $parameters = [])
    {
        $loader = new ArrayLoader(['base.html' => $template]);
        $twig = new Environment($loader);
        $twig->addGlobal('user_ip', Request::ip());
        $twig->addGlobal('user_agent', Request::userAgent());

        return $twig->render('base.html', $parameters);
    }
}
