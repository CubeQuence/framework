<?php

namespace CQ\Routing;

use MiladRahimi\PhpRouter\Router as RouterClass;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use Zend\Diactoros\Response\RedirectResponse;

class Router
{
    private $router;

    /**
     * Create router instance
     *
     * @param string $controllers
     * 
     * @return void
     */
    public function __construct($controllers = 'App\Controllers')
    {
        $this->router = new RouterClass('', $controllers);
    }

    /**
     * Return router instance
     *
     * @return RouterClass
     */
    public function get()
    {
        return $this->router;
    }

    /**
     * Start the router
     *
     * @return void
     */
    public function start()
    {
        try {
            $this->router->dispatch();
        } catch (RouteNotFoundException $e) {
            $this->router->getPublisher()->publish(new RedirectResponse('/error/404', 404));
        } catch (\Throwable $e) {
            $this->router->getPublisher()->publish(new RedirectResponse("/error/500?e={$e}", 500));
        }
    }
}
