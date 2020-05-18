<?php

namespace CQ\Routing;

use CQ\Helpers\App;
use MiladRahimi\PhpRouter\Router as RouterBase;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use Zend\Diactoros\Response\RedirectResponse;

class Router
{
    private $router;
    private $route404;
    private $route500;

    /**
     * Create router instance
     *
     * @param array $config
     * @param string $controllers
     * 
     * @return void
     */
    public function __construct($config = [], $controllers = 'App\Controllers')
    {
        $this->router = new RouterBase('', $controllers);
        $this->route404 = $config['404'] ?: '/';
        $this->route500 = $config['500'] ?: '/';
    }

    /**
     * Return router instance
     *
     * @return RouterBase
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
            $this->router->getPublisher()->publish(new RedirectResponse($this->route404, 404));
        } catch (\Throwable $e) {
            if (!App::debug()) {
                $this->router->getPublisher()->publish(new RedirectResponse("{$this->route500}?e={$e}", 500));
            }
        }
    }
}
