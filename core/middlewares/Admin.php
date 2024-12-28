<?php

namespace core\middlewares;

use core\App;
use core\Controller;

class Admin
{
    public function handle()
    {
        if (App::user()['role'] != "admin")
            Controller::showView('errors/error', ['code'=>403, 'message'=>'В доступі відмовлено']);
    }
}