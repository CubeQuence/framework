<?php

namespace CQ\Response;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
        $loader = new FilesystemLoader('../views');
        if ($cache) {
            $this->twig = new Environment($loader, ['cache' => '../storage/views']);
        } else {
            $this->twig = new Environment($loader);
        }
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
