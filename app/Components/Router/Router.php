<?php

namespace App\Components\Router;

use App\Components\Exception\CustomException;

class Router
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    private $namedRoutes = [];

    /**
     * Router constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Ajout de la route dans la liste des routes
     *
     * @param array $routeConfig
     * @return Route
     */
    public function add(array $routeConfig): Route
    {
        $route = new Route($routeConfig);

        if (is_string($routeConfig['methods'])) {
            $this->routes[strtoupper($routeConfig['methods'])][] = $route;
        } else {
            foreach ($routeConfig['methods'] as $method) {
                $this->routes[strtoupper($method)][] = $route;
            }
        }

        if (isset($routeConfig['name'])) {
            $this->namedRoutes[$routeConfig['name']] = $route;
        }

        return $route;
    }

    /**
     * Exécution de la route actuelle
     *
     * @return mixed
     * @throws CustomException
     */
    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new CustomException('REQUEST_METHOD does not exist');
        }

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }

        throw new CustomException('No matching routes');
    }
}