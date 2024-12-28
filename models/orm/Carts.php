<?php

namespace models\orm;

use core\App;
use core\database\DB;
use core\database\DBModel;
use Exception;
use models\Cart;
use models\CartItem;

class Carts extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Cart');
    }

    /**
     * @param $uid
     * @param CartItem $data
     * @return void
     */
    public function append($uid, $data)
    {
        $cart = $this->db->find($this->table)->select('*')->where('user_id', '=', $uid)->row();

        if (!$cart){
            $this->db->insert($this->table)
                ->fields(['user_id'])
                ->value([$uid])
                ->run();
        }
        $cart_id = $this->db->find($this->table)->select('*')->where('user_id', '=', $uid)->row()['cart_id'];

        $cart_item = $this->db->find('Cart_Item')
            ->select('*')
            ->where('product_id', '=', $data->productId())
            ->where('cart_id', '=', $cart_id)
            ->row();

        if (!$cart_item){
            $this->db->insert('Cart_Item')
                ->fields(['cart_id','product_id','quantity'])
                ->value([$cart_id, $data->productId(), $data->quantity()])
                ->run();
        }else{
            $this->db->update('Cart_Item')
                ->fields(['cart_id','product_id','quantity'])
                ->value([$cart_id, $data->productId(), $cart_item['quantity']+$data->quantity()])
                ->where('cart_id', '=', $cart_id)
                ->where('product_id', '=', $data->productId())
                ->run();
        }

    }

    /**
     * @return Cart|Cart[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])){
            $rows = $this->db->find($this->table)->select('*')->where('cart_id','in', '('. implode(',',$key) .')')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('cart_id','=',$key)->row());
        }

        return $this->rowsToClass($rows);
    }

    /**
     * @return Cart|Cart[]
     */
    public function getUserCart($key)
    {
        if (!is_numeric($key)){
            return null;
        }

        $cart = $this->db->find($this->table)->select('*')->where('user_id','=',$key)->row();

        return $cart ? $this->rowToClass($cart) : null;
    }

    /**
     * @param int $key
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            $this->db->delete('Cart_Item')->where('cart_id', '=', $key)->run();
            $this->db->delete($this->table)->where('cart_id','=',$key)->run();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    public function removeCartItem($cartItem_id)
    {
        $this->db->delete('Cart_Item')->where('cart_item_id', '=', $cartItem_id)->run();
    }

    public function placeOrder($card_id, $user_id, $address)
    {
        $cart = $this->get($card_id);

        $date = date('Y-m-d H:i:s');

        $this->db->insert('Ordering')
            ->fields([
                'user_id',
                'order_date',
                'total_amount',
                'discount_applied',
                'final_amount',
                'status',
                'delivery_address',
                'total_order_price'
            ])
            ->value([
                $user_id,
                $date,
                $cart->totalPriceWithoutDiscount(),
                $cart->totalDiscount(),
                $cart->totalPrice(),
                'Нове',
                $address,
                $cart->finalePrice()
            ])
            ->run();

        $order_id = $this->db->find('Ordering')
            ->select('order_id')
            ->where('user_id', '=', $user_id)
            ->where('order_date', '=', $date)
            ->row()['order_id'];

        /** @var CartItem $cartItem */
        foreach ($cart->cartItems() as $cartItem){
            $this->db->insert('Order_Item')
                ->fields([
                    'order_id',
                    'product_id',
                    'quantity',
                    'price_at_order_time'
                ])
                ->value([
                    $order_id,
                    $cartItem->productId(),
                    $cartItem->quantity(),
                    $cartItem->product()->price()
                ])
                ->run();
        }

        $this->delete($card_id);
    }

    protected function rowToClass($row): Cart
    {
        $cart = new Cart(
            $row['cart_id'],
            $row['user_id']
        );

        /** @var Products $products*/
        $products = App::resolve(Products::class);

        $items = $this->db->find('Cart_item')->where('cart_id', '=', $row['cart_id'])->rows();

        foreach ($items as $item){
            $cart->appendCartItem(new CartItem(
                $item['cart_item_id'],
                $item['cart_id'],
                $item['product_id'],
                $item['quantity'],
                $products->get($item['product_id'])
            ));
        }
        return $cart;
    }

}