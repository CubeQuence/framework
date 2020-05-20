<?php

namespace CQ\Response;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig
{
    private $twig;

    /**
     * Create twig instance
     *
     * @param bool $cache
     * 
     * @return void
     */
    public function __construct($cache = true)
    {
        $loader = new FilesystemLoader('../resources/views');
        if ($cache) {
            $this->twig = new Environment($loader, ['cache' => '../storage/views']);
        } else {
            $this->twig = new Environment($loader);
        }

        $function = new TwigFunction('asset', function ($asset) {
            $manifest = file_get_contents("asset-manifest.json");
            $manifest = json_decode($manifest, true);

            if (!isset($manifest[$asset])) {
                return $asset;
            };

            return $manifest[$asset];
        });
        $this->twig->addFunction($function);
    }

    /**
     * Return twig instance
     *
     * @return Environment
     */
    public function get()
    {
        return $this->twig;
    }
}
