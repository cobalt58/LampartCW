<?php

use core\App;
use core\router\DefaultRouteHandler;
use core\router\Router;

$router = new Router();
$router->setRouteHandler(new DefaultRouteHandler());

$router->addRoute('/', 'Головна', 'home');
$router->addRoute('/shop', 'Магазин', 'shop')->navVisible(true);
$router->addRoute('/cart', 'Кошик', 'cart')->navVisible(true);

$router->addRoute('/sign-up', 'Реєстрація', 'reg')->only('guest')->navVisible(false);
$router->addRoute('/sign-in', 'Авторизація', 'auth')->only('guest')->navVisible(false);
$router->addRoute('/profile', 'Профіль', 'profile')->only('auth')->navVisible(false);

$router->addRoute('/admin', 'Статистика', 'admin')->only('admin')->navVisible(false);
$router->addRoute('/admin/products', 'Продукти', 'products')->only('admin');
$router->addRoute('/admin/categories', 'Категорії', 'categories')->only('admin');
$router->addRoute('/admin/users', 'Користувачі', 'users')->only('admin');
$router->addRoute('/admin/properties', 'Характеристики', 'properties')->only('admin');
$router->addRoute('/admin/promotions', 'Знижки', 'promotions')->only('admin');
$router->addRoute('/admin/discountSchemes', 'Схеми накоп. знижок', 'promotions')->only('admin');
$router->addRoute('/admin/carts', 'Схеми накоп. знижок', 'promotions')->only('auth');
$router->addRoute('/admin/orders', 'Замовлення', 'promotions')->only('admin');


$router->addRoute('/api', 'api', 'api')->navVisible(false);
$router->addRoute('/api/categories', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/users', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/properties', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/products', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/promotions', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/discountSchemes', 'api', 'apiUsers')->only('auth')->navVisible(false);
$router->addRoute('/api/orders', 'api', 'apiUsers')->only('auth')->navVisible(false);

App::setRouter($router);

