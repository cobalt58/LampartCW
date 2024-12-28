<?php

namespace core\middlewares;

use core\App;

class Auth
{
    public function handle()
    {
        if (!App::isAuth()) redirect('/sign-in');
    }
}