<?php

namespace CQ\Response;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

use CQ\Config\Config;
use CQ\Helpers\App;

class Twig
{
    private Environment $twig;

    /**
     * Create twig instance.
     */
    public function __construct()
    {
        $cache_enabled = Config::get(key: 'cache.views') && !App::debug();
        $loader = new FilesystemLoader(paths: '../views');

        $twig = new Environment(
            loader: $loader,
            options: [
                'cache' => $cache_enabled ? '../storage/views' : false,
            ]
        );

        $this->twig = self::addGlobals($twig);
    }

    /**
     * Add global parameters to twig
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    private static function addGlobals(Environment $twig) : Environment
    {
        $twig->addGlobal(
            name: 'app',
            value: Config::get(key: 'app')
        );

        $twig->addGlobal(
            name: 'analytics',
            value: Config::get(key: 'analytics')
        );

        return $twig;
    }

    /**
     * Render twig template
     *
     * @param string $view
     * @param array $parameters
     *
     * @return string
     */
    public function render(string $view, array $parameters) : string
    {
        return $this->twig->render(
            name: $view,
            context: $parameters
        );
    }

    /**
     * Render template in string form.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return string
     */
    public static function renderFromText(string $template, array $parameters = []) : string
    {
        $loader = new ArrayLoader(
            templates: ['base.html' => $template]
        );

        $twig = new Environment(loader: $loader);
        $twig = self::addGlobals(twig: $twig);

        return $twig->render(
            name: 'base.html',
            context: $parameters
        );
    }
}
