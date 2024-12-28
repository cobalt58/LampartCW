<?php

namespace core\router;

use core\exceptions\NotFoundException;
use Exception;

class Router
{

    /**
     * @var Route[]
     */
    protected array $routes;
    protected Route $route;
    private RouteHandler $routeHandler;


    /**
     * @param $url
     * Base url for route
     * @param $title
     * Title for page
     * @param $controller
     * Controller name. Example: home -> HomeController
     * @return $this
     */
    public function addRoute($url, $title, $controller): Router
    {
        $route = null;

        try {
            $route = new Route($url, $title, $controller);
        } catch (Exception $e) {
            clog("{$e->getCode()} {$e->getMessage()}");
            abort(404, 'Ой что-то не так.');
        }

        $this->routes[$url] = $route;

        return $this;
    }

    /**
     * @param RouteHandler $handler
     * @return void
     */
    public function setRouteHandler(RouteHandler $handler) {
        $this->routeHandler = $handler;
    }

    public function only($key): Router
    {
        $this->routes[array_key_last($this->routes)]->setMiddleware($key);
        switch ($key){
            case 'admin': $key = 100; break;
            case 'auth':  $key = 10; break;
            case 'guest':
            default: $key = 0; break;
        }
        $this->routes[array_key_last($this->routes)]->setAcl($key);
        return $this;
    }

    public function navVisible(bool $navVisible): Router
    {
        $this->routes[array_key_last($this->routes)]->setNavVisible($navVisible);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function navigate($url): Route
    {
        $parsedUrl = parse_url($url);

        $params = explode('/', trim($parsedUrl['path'],'/'));

        $url = "/{$params[0]}";
        $method = $params[1] ?? 'index';
        $attrs = array_splice($params, 2);

        if (!array_key_exists($url, $this->routes)){
            throw new NotFoundException('Page not found', 404);
        }

        $route = $this->routes[$url];

        $route->setMethod($method);
        $route->setParams($attrs);
        $this->route = $route;

        $this->routeHandler->handle($route);

        return $this->route;
    }

    public function previousUrl(): string
    {
        return $_SERVER['HTTP_REFERER'];
    }
    
    /**
     * @return Route[]
     */
    public function routes(): array
    {
        return $this->routes;
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        if (is_string($route))
            $this->route = $this->routes[$route];
        if ($route instanceof Route)
            $this->route = $route;

    }

}