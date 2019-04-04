<?php

namespace App\Components\Router;

use App\Components\Api\Api;
use App\Components\Auth\Auth;

class Route
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * @var bool
     */
    private $mustBeLogged;

    /**
     * @var bool
     */
    private $isApi;

    /**
     * @var array
     */
    private $matches = [];

    /**
     * @var array
     */
    private $params = [];

    /**
     * Route constructor.
     *
     * @param array $route
     */
    public function __construct(array $route)
    {
        $this->path = trim($route['pattern'], '/');
        $this->isApi = $route['type'] === 'api';
        $this->controller = $route['controller'] ?? null;
        $this->action = $route['action'];
        $this->mustBeLogged = $route['mustBeLogged'] ?? false;
    }

    /**
     * Capturer l'url avec les paramètre
     * get('/posts/:slug-:id') par exemple
     *
     * @param string $url
     * @return bool
     */
    public function match(string $url): bool
    {
        $url = trim($url, '/');
        $cleanedPath = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$cleanedPath$#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    /**
     * @param array $match
     * @return string
     */
    private function paramMatch(array $match): string
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    /**
     * Appel de la route
     *
     * @return mixed
     */
    public function call()
    {
        if ($this->isApi) {
            $api = new Api($this->action);
            $api->processApi();
        } else {
            $fullController = "App\\Controllers\\" . $this->controller . "Controller";
            $fullController = new $fullController();
            if ($this->mustBeLogged && !Auth::logged()) {
                Auth::forbidden();
            } else {
                return call_user_func_array([$fullController, $this->action], $this->matches);
            }
        }
    }
}