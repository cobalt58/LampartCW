<?php
namespace core\router;

use core\exceptions\NotFoundException;
use Exception;

class Route
{
    protected string $base_url;
    protected string $title;
    protected string $controller;
    protected array $params;
    protected int $acl = 0;
    protected string $method = 'index';
    protected string $middleware = '';
    protected bool $navVisible = true;

    /**
     * @param string $base_url Base url for route
     * @param string $title Title for page
     * @param string $controller Controller name. Example: home -> HomeController
     * @throws Exception If controller class not found or invalid name
     */
    public function __construct(string $base_url, string $title, string $controller)
    {
        $this->base_url = $base_url;
        $this->title = $title;

        if (!preg_match('/^[a-z\d]+$/i', $controller)) {
            throw new NotFoundException('Error name controller', 404);
        }
        $controller = 'controllers\\'. ucfirst($controller).'Controller';

        $this->controller = $controller;
    }

    public function setAcl(int $acl)
    {
        $this->acl = $acl;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function controller(): string
    {
        return $this->controller;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function method(): string
    {
        return $this->method;
    }

    /**
     * @return string Base route URL
     */
    public function url(): string
    {
        return $this->base_url;
    }

    /**
     * @return int Route access level >= 0 and <= 100
     */
    public function acl(): int
    {
        return $this->acl;
    }

    public function setMiddleware(string $key)
    {
        $this->middleware = $key;
    }

    public function middleware(): string
    {
        return $this->middleware;
    }

    public function isNavVisible(): bool
    {
        return $this->navVisible;
    }

    public function setNavVisible(bool $navVisible): void
    {
        $this->navVisible = $navVisible;
    }

    public function params(): array
    {
        return $this->params;
    }
}