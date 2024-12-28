<?php

namespace models;

use core\App;
use models\orm\DiscountSchemes;

class Cart
{
    private $id;
    private $user_id;
    private array $cart_items;

    /**
     * @param $id
     * @param $user_id
     */
    public function __construct($id, $user_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->cart_items = [];
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function userId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    public function cartItems(): array
    {
        return $this->cart_items;
    }

    public function appendCartItem(CartItem $item): void
    {
        $this->cart_items[] = $item;
    }

    public function setCartItems(array $cart_items): void
    {
        $this->cart_items = $cart_items;
    }

    public function countItems(): int
    {
        return count($this->cart_items);
    }

    public function totalPrice()
    {
        $total_price = 0;
        /** @var CartItem $cart_item */
        foreach ($this->cart_items as $cart_item) {
            $total_price += $cart_item->totalPrice();
        }
        return $total_price;
    }

    public function totalPriceWithoutDiscount()
    {
        $total_price = 0;
        /** @var CartItem $cart_item */
        foreach ($this->cart_items as $cart_item) {
            $total_price += $cart_item->totalPriceWithoutDiscount();
        }
        return $total_price;
    }

    public function totalDiscount()
    {
        $total_discount = 0;
        /** @var CartItem $cart_item */
        foreach ($this->cart_items as $cart_item) {
            $total_discount += $cart_item->product()->price() - $cart_item->product()->priceWithDiscount();
        }
        return $total_discount;
    }

    public function finalePrice()
    {
        /** @var DiscountSchemes $schemes */
        $schemes = App::resolve(DiscountSchemes::class);

        return $this->totalPrice() - $this->totalPrice() * ($schemes->getDiscount($this->user_id) / 100);

    }

}