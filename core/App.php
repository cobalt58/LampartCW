<?php
namespace core;

use core\exceptions\NotFoundException;
use core\exceptions\ServerException;
use core\router\Route;
use core\router\Router;
use core\validation\ValidationException;
use core\visitor\AdminVisitor;
use core\visitor\GuestVisitor;
use core\visitor\UserVisitor;
use Exception;

class App
{
    const MODE_DEV = 'dev';
    const MODE_PROD = 'prod';
    protected static ?array $user = null;
    protected static ?Route $route = null;
    protected static Container $container;
    protected static Router $router;
    private static string $mode;

    public static function Run()
    {
        try {
            static::$router->navigate($_SERVER['REQUEST_URI']);
        }
        catch (NotFoundException $notFoundException){
            clog("{$notFoundException->getCode()} {$notFoundException->getMessage()}");
            abort($notFoundException->getCode());
        }
        catch (ServerException $serverException){
            clog("{$serverException->getCode()} {$serverException->getMessage()}");
            abort($serverException->getCode());
        }
        catch (ValidationException $validationException){
            Session::flash('errors', $validationException->errors());
            Session::flash('old', $validationException->old());

            redirect(static::$router->previousUrl());
        }

//        catch (Exception $e) {
//            clog("{$e->getCode()} {$e->getMessage()}");
//            abort($e->getCode());
//        }

        Session::unflash();
    }

    public static function isGuest(): bool
    {
        return !Session::has('user');
    }

    public static function isUser(): bool
    {
        return Session::has('user');
    }

    public static function isAdmin(): bool
    {
        return Session::has('user') && Session::get('user')['role'] == "admin";
    }

    public static function visitor()
    {
        return App::isGuest() ? new GuestVisitor() : (App::isAdmin() ? new AdminVisitor() : new UserVisitor());
    }

    /**
     * @param string $key
     * @param $resolver
     */
    public static function bind(string $key, $resolver)
    {
        static::container()->bind($key, $resolver);
    }

    public static function resolve($key)
    {
        try {
            return static::container()->resolve($key);
        } catch (Exception $e) {
            clog('Can`t resolve data from app container');
            abort(505, 'Щось явно не так, хм...');
            exit();
        }
    }

    /**
     * @return array
     */
    public static function user(): ?array
    {
        return static::$user;
    }

    /**
     * @param array $user
     */
    public static function setUser(array $user)
    {
        static::$user = $user;
    }

    public static function router(): Router
    {
        return static::$router;
    }

    /**
     * @return Route
     */
    public static function route(): ?Route
    {
        return static::$router->route();
    }

    /**
     * @param Route|string $route
     */
    public static function setRoute($route)
    {
        static::$router->setRoute($route);
    }

    public static function setRouter(Router $router): void
    {
        static::$router = $router;
    }

    /**
     * @return Route[]
     */
    public static function routes(): array
    {
        return static::$router->routes();
    }

    /**
     * @param Container $container
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    /**
     * @return Container
     */
    public static function container(): Container
    {
        return static::$container;
    }


    /**
     * Returns true if user or admin auth on page, otherwise return false
     * @return bool
     */
    public static function isAuth(): bool
    {
        return App::isUser() || App::isAdmin();
    }

    public static function mode($set = null)
    {
        if (is_null($set))
            return static::$mode;
        else{
            static::$mode = $set;
        }
    }
}