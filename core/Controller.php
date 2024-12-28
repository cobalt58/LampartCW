<?php
namespace core;
class Controller
{
    public function view($view, $options = []) {
        extract($options);
        include(base_path("views/{$view}.view.php"));
    }

    public static function showView($view, $options = []) {
        extract($options);
        include(base_path("views/{$view}.view.php"));
        die();
    }
}