<?php

namespace controllers;

use core\App;
use core\auth\Authenticator;
use core\Controller;
use models\orm\Orders;
use models\orm\Users;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var Users $users */
        $users = App::resolve(Users::class);

        /** @var Orders $orders */
        $orders = App::resolve(Orders::class);

        $user = $users->get(App::user()['id']);
        $userOrders = $orders->getUsersOrders(App::user()['id']);

        $this->view('profile/index', [
            'user'=>[
                'id'=>$user->id(),
                'name'=>$user->name(),
                'surname'=>$user->surname(),
                'middlename'=>$user->patronymic(),
                'email'=>$user->email(),
                'phone'=>$user->phone(),
                'role'=>$user->role(),
            ],
            'orders'=>$userOrders,
        ]);
    }

    public function logout()
    {
        Authenticator::logout();
    }
}