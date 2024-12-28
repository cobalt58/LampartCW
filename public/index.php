<?php

use core\App;
use core\Uploader;
use models\orm\Users;

session_start();
const BASE_PATH = __DIR__ . '\\..\\';

require_once '../core/functions.php';

spl_autoload_register(function ($class){
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path( "{$class}.php");
});

require base_path('routes.php');
require base_path('bootstrap.php');


App::mode(App::MODE_DEV);
App::setUser($_SESSION['user'] ?? [
    'login' => 'GUEST',
    'auth' => false,
    'acl' => 0,
]);

App::Run();

