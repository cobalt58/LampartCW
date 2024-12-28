<?php

namespace controllers;

use core\App;
use core\Controller;
use models\orm\Carts;

class CartController extends Controller
{
    public function index()
    {
        /** @var Carts $carts */
        $carts = App::resolve(Carts::class);
        $cart = $carts->getUserCart(App::user()['id']);
        $this->view('cart/index', ['cart' => $cart]);
    }
}