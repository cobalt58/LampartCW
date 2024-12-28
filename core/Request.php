<?php
namespace core;
class Request
{
    private static array $TF = [
            'true'=>true,
            'false'=>false
        ];
    static function post($key, $def = null) {
        return (isset($_POST[$key]))
            ? (is_string($_POST[$key]) || is_int($_POST[$key])) && array_key_exists($_POST[$key], Request::$TF)
                ? Request::$TF[$_POST[$key]]
                : $_POST[$key]
            : $def;
    }
    static function get($key, $def = null) {
        return (isset($_GET[$key]))
            ? (is_string($_GET[$key]) || is_int($_GET[$key])) &&array_key_exists($_GET[$key], Request::$TF)
                ? Request::$TF[$_GET[$key]]
                : $_GET[$key]
            : $def;
    }

    static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    static function PostOrRedirect($url = null)
    {
        if (!static::isPost()) redirect($url ?? App::router()->previousUrl());
    }
    static function PostAndRedirect($url = null)
    {
        if (static::isPost()) redirect($url ?? App::router()->previousUrl());
    }

    static function GetOrRedirect($url = null)
    {
        if (!static::isGet()) redirect($url ?? App::router()->previousUrl());
    }
    static function GetAndRedirect($url = null)
    {
        if (static::isGet()) redirect($url ?? App::router()->previousUrl());
    }
}
