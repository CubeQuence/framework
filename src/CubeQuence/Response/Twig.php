<?php

declare(strict_types=1);

namespace CQ\Response;

use CQ\Helpers\AppHelper;
use CQ\Helpers\ConfigHelper;
use CQ\Helpers\StateHelper;
use Twig\Environment;
use Twig\TwigFunction;
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
        $cacheEnabled = ConfigHelper::get(key: 'cache.views') && !AppHelper::isDebug();
        $loader = new FilesystemLoader(paths: '../views');

        $twig = new Environment(
            loader: $loader,
            options: [
                'cache' => $cacheEnabled ? '../storage/views' : false,
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
    ): string {
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
        $appConfig = ConfigHelper::get(key: 'app');
        unset($appConfig['key']);

        $twig->addGlobal(
            name: 'app',
            value: $appConfig
        );

        $twig->addGlobal(
            name: 'analytics',
            value: ConfigHelper::get(key: 'analytics')
        );

        // Support for {{ csrf_token() }}
        $twig->addFunction(
            new TwigFunction(
                name: 'csrf_token',
                callable: function () {
                    return StateHelper::set();
                }
            )
        );

        // Support for {{ csrf_input() }}
        $twig->addFunction(
            new TwigFunction(
                name: 'csrf_input',
                callable: function () {
                    $token = StateHelper::set();

                    return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\" />";
                },
                options: [
                    'is_safe' => [
                        'html'
                    ]
                ]
            )
        );

        return $twig;
    }
}
