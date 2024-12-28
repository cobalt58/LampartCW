<?php

namespace controllers;

use controllers\ApiController;
use core\App;
use core\Request;
use models\CartItem;
use models\orm\Carts;
use models\orm\Categories;

class ApiCartsController extends ApiController
{
    protected Carts $carts;

    public function __construct()
    {
        $this->carts = App::resolve(Carts::class);
    }

    public function getCart()
    {
        $this->success('Successfully fetched cart', [
            'cart'=>$this->dismount($this->carts->getUserCart(App::user()['id']))
        ]);
    }

    public function appendCart()
    {
        $product_id = Request::post('product_id');
        $quantity = Request::post('quantity');
        if (!$product_id || !$quantity) {
            $this->error(422, 'Invalid arguments', ['errors'=>['Incorrect arguments']]);
            die();
        }

        $this->carts->append(App::user()['id'], new CartItem(-1, -1, $product_id, $quantity));

        $this->success('Successfully append to cart');
    }

    public function confirmOrder()
    {
        $user_id = Request::post('user_id');
        $cart_id = Request::post('cart_id');
        $address = Request::post('address');
        if (!$user_id || !$cart_id) {
            $this->error(422, 'Invalid arguments', ['errors'=>['Incorrect arguments']]);
            die();
        }

        $this->carts->placeOrder($cart_id, $user_id, $address);

        $this->success('Order successfully placed');
    }

    public function removeCartItem()
    {
        $cartItem_id = Request::post('cart_item_id');
        if (!$cartItem_id) {
            $this->error(422, 'Invalid arguments', ['errors'=>['Incorrect arguments']]);
            die();
        }

        $this->carts->removeCartItem($cartItem_id);

        $this->success('Successfully removed item from cart');
    }

    public function deleteCart(){
        $this->carts->delete($this->carts->getUserCart(App::user()['id'])->id());
        $this->success('Successfully deleted cart', []);
    }

}