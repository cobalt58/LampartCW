<?php

use core\App;

function urlIs ($value): bool
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function findFileByName(string $directory, string $filenameWithoutExtension): ?string
{
    // Check if the directory exists
    if (!is_dir($directory)) {
        throw new InvalidArgumentException("The provided path is not a valid directory.");
    }

    // Open the directory
    $files = scandir($directory);

    foreach ($files as $file) {
        // Skip special directories '.' and '..'
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Get the file name without extension
        $nameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);

        // Check if it matches the given filename
        if ($nameWithoutExtension === $filenameWithoutExtension) {
            return $file;
        }
    }

    // Return null if the file is not found
    return null;
}

function getImagesFromDirectory(string $directory): array
{
    // Check if the directory exists
    if (!is_dir($directory)) {
        return [];
    }

    $images = [];

    // Supported image file extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'ico'];

    // Scan the directory for files
    $files = scandir($directory);

    foreach ($files as $file) {
        // Skip special directories '.' and '..'
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Get the file's extension
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        // Check if the file is an image
        if (in_array($extension, $allowedExtensions)) {
            $images[] = $file;
        }
    }

    return $images;
}

function generateSalt($length = null): string
{
    $salt = "";
    if ($length == null) $length = 10;
    for ($i=0; $i < $length; $i++) {
        $int = 0;
        while(true){
            $int = mt_rand(33, 126);
            if (!in_array($int, [34, 39, 47, 96, 92])) break;
        }
        $salt .= chr($int);
    }
    return $salt;
}

function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

function getStatus($request): string
{
    if ($request instanceof \models\Request)
        return $request->status() == 1 ? 'Схвалено': ($request->status() == -1 ? 'Відхилено': ($request->status() == 0 ? 'В обробці':'Невідомий'));

    if (is_numeric($request))
        return $request == 1 ? 'Схвалено': ($request == -1 ? 'Відхилено': ($request == 0 ? 'В обробці':'Невідомий'));

    return 'Невідомий';
}

function devLog($value)
{
    if (App::mode() == APP::MODE_DEV){
        dd($value);
    }
}

function clog($str)
{
    echo "<script>console.log('{$str}')</script>";
}

function abort($code = 404, $message = '')
{
    http_response_code($code);
    require base_path("views/errors/{$code}.view.php");
    die();
}

function getStaticResource($url): string
{
    return "http://{$_SERVER['HTTP_HOST']}/public/$url";
}

function deleteDirectory($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    deleteDirectory($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
    }
}

function dismount($object)
{
    if (is_array($object)) {
        $array = array_map(function($value){
            return dismount($value);
        }, $object);
    }elseif (!is_object($object)){
        return $object;
    }
    else {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (is_object($value) && !($value instanceof DateTime)) {
                $array[$property->getName()] = dismount($value);
            } else {
                $array[$property->getName()] = $value;
            }
            $property->setAccessible(false);
        }
    }
    return $array;
}

function base_path($path): string
{
    return BASE_PATH . $path;
}

function font_path($font_name){
    return realpath(base_path("core/fonts/{$font_name}.ttf"));
}

function redirect($path)
{
    header("Location: {$path}");
    exit();
}

function old($key, $default)
{
    return \core\Session::get('old')['key'] ?? $default;
}