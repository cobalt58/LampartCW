<?php

namespace core\router;

class ApiRouteHandler implements RouteHandler {
    public function handle(Route $route) {
        // Обробка маршруту для API
        echo "API Route: " . $route->controller() . "::" . $route->method();
    }
}
