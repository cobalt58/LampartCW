<?php

namespace core\middlewares;

use Exception;

class Middleware
{
    public const MAP = [
        'admin' => Admin::class,
        'guest' => Guest::class,
        'auth' => Auth::class,
    ];

    /**
     * @throws Exception If middleware no matching
     */
    public static function resolve($key)
    {
        if (!$key) return;
        $middleware = static::MAP[$key] ?? false;

        if (!$middleware) throw new Exception("No matching middleware for key {$key}.");

        (new $middleware)->handle();
    }
}