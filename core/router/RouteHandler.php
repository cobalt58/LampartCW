<?php

namespace core\router;

interface RouteHandler
{
    public function handle(Route $route);
}