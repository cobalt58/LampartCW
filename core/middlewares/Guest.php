<?php

namespace core\middlewares;

use core\App;

class Guest
{
    public function handle()
    {
        if (!App::isGuest()) redirect('/profile');
    }
}