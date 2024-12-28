<?php

namespace models;

class OrderItem
{
    private $id;
    private $order_id;
    private $product_id;
    private $quantity;
    private $price_at_order_time;

    private $product;

    /**
     * @param $id
     * @param $order_id
     * @param $product_id
     * @param $quantity
     * @param $price_at_order_time
     */
    public function __construct($id, $order_id, $product_id, $quantity, $price_at_order_time, $product)
    {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->price_at_order_time = $price_at_order_time;
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
    public function orderId()
    {
        return $this->order_id;
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id): void
    {
        $this->order_id = $order_id;
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

    /**
     * @return mixed
     */
    public function priceAtOrderTime()
    {
        return $this->price_at_order_time;
    }

    /**
     * @param mixed $price_at_order_time
     */
    public function setPriceAtOrderTime($price_at_order_time): void
    {
        $this->price_at_order_time = $price_at_order_time;
    }

    /**
     * @return mixed
     */
    public function product()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }


}