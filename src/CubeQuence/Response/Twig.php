<?php

declare(strict_types=1);

namespace CQ\Response;

use CQ\Config\Config;
use CQ\Helpers\App;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

final class Twig
{
    private Environment $twig;

    /**
     * Create twig instance.
     */
    public function __construct()
    {
        $cache_enabled = Config::get(key: 'cache.views') && ! App::debug();
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
     * Render twig template
     */
    public function render(
        string $view,
        array $parameters
    ): string
    {
        return $this->twig->render(
            name: $view,
            context: $parameters
        );
    }

    /**
     * Render template in string form.
     */
    public static function renderFromText(
        string $template,
        array $parameters = []
    ): string {
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

    /**
     * Add global parameters to twig
     */
    private static function addGlobals(Environment $twig): Environment
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
}
