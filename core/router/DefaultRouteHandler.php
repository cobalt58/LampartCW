<?php

namespace core\router;

use core\exceptions\ServerException;
use core\middlewares\Middleware;
use Exception;

class DefaultRouteHandler implements RouteHandler {

    /**
     * @param Route $route
     * @throws ServerException
     * @throws Exception
     */
    public function handle(Route $route)
    {
        $controllerName = $route->controller();

        $file = base_path($controllerName . '.php');
        if (!is_file($file)){
            throw new ServerException('Controller file not found', 505);
        }

        $c = new $controllerName();

        if (isset($method) && !method_exists($c, $method)){
            throw new ServerException('Method not found', 505);
        }

        if (!empty($route->middleware())){
            Middleware::resolve($route->middleware());
        }

        call_user_func(array($c, $route->method()));
    }
}