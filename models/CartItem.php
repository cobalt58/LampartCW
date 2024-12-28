<?php

namespace models;

class CartItem
{
    private $id;
    private $cart_id;
    private $product_id;
    private $quantity;
    private ?Product $product;

    /**
     * @param $id
     * @param $cart_id
     * @param $product_id
     * @param $quantity
     */
    public function __construct($id, $cart_id, $product_id, $quantity, $product = null)
    {
        $this->id = $id;
        $this->cart_id = $cart_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->product = $product;
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
    public function cartId()
    {
        return $this->cart_id;
    }

    /**
     * @param mixed $cart_id
     */
    public function setCartId($cart_id): void
    {
        $this->cart_id = $cart_id;
    }

    /**
     * @return mixed
     */
    public function productId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id): void
    {
        $this->product_id = $product_id;
    }

    /**
     * @return mixed
     */
    public function quantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function totalPrice(){
        return $this->quantity * $this->product->priceWithDiscount();
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function totalPriceWithoutDiscount()
    {
        return $this->quantity * $this->product->price();
    }
}