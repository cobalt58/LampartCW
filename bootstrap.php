<?php

use core\App;
use core\Container;
use core\database\DB;
use models\orm\Carts;
use models\orm\DiscountSchemes;
use models\orm\Orders;
use models\orm\Products;
use models\orm\Promotions;
use models\orm\Properties;
use models\orm\Users;
use models\orm\Categories;

$container = new Container();

$container->singleton('core\database\DB', function (){
    $db_config = require base_path('config.php');
    return new DB($db_config);
});

$container->bind('models\orm\Users', function () use ($container){
    return new Users($container->resolve(Db::class));
});

$container->bind('models\orm\Properties', function () use ($container){
    return new Properties($container->resolve(Db::class));
});
$container->bind('models\orm\Carts', function () use ($container){
    return new Carts($container->resolve(Db::class));
});
$container->bind('models\orm\Orders', function () use ($container){
    return new Orders($container->resolve(Db::class));
});
$container->bind('models\orm\Products', function () use ($container){
    return new Products($container->resolve(Db::class));
});
$container->bind('models\orm\DiscountSchemes', function () use ($container){
    return new DiscountSchemes($container->resolve(Db::class));
});
$container->bind('models\orm\Promotions', function () use ($container){
    return new Promotions($container->resolve(Db::class));
});
$container->bind('models\orm\Categories', function () use ($container){
    return new Categories($container->resolve(Db::class));
});

$container->bind('models\orm\Roles', function () use ($container){
    return new Roles($container->resolve(Db::class));
});

$container->bind('models\orm\Categories', function () use ($container){
    return new Categories($container->resolve(Db::class));
});

$container->bind('models\orm\Places', function () use ($container){
    return new Places($container->resolve(Db::class));
});

$container->bind('models\orm\Requests', function () use ($container){
    return new Requests($container->resolve(Db::class));
});

$container->bind('models\orm\Likes', function () use ($container){
    return new Likes($container->resolve(Db::class));
});

$container->bind('models\orm\Reviews', function () use ($container){
    return new Reviews($container->resolve(Db::class));
});

App::setContainer($container);
