<?php

namespace models;

class Order
{
    private $id;
    private $user_id;
    private $order_date;
    private $total_amount;
    private $discount_applied;
    private $status;
    private $delivery_address;
    private $total_order_price;
    private array $order_items;
    private $user;

    /**
     * @param $id
     * @param $user_id
     * @param $order_date
     * @param $total_amount
     * @param $discount_applied
     * @param $status
     * @param $delivery_address
     * @param $total_order_price
     */
    public function __construct($id, $user_id, $order_date, $total_amount, $discount_applied, $status, $delivery_address, $total_order_price, $user)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->order_date = $order_date;
        $this->total_amount = $total_amount;
        $this->discount_applied = $discount_applied;
        $this->status = $status;
        $this->delivery_address = $delivery_address;
        $this->total_order_price = $total_order_price;
        $this->user = $user;
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

    /**
     * @return mixed
     */
    public function orderDate()
    {
        return $this->order_date;
    }

    /**
     * @param mixed $order_date
     */
    public function setOrderDate($order_date): void
    {
        $this->order_date = $order_date;
    }

    /**
     * @return mixed
     */
    public function totalAmount()
    {
        return $this->total_amount;
    }

    /**
     * @param mixed $total_amount
     */
    public function setTotalAmount($total_amount): void
    {
        $this->total_amount = $total_amount;
    }

    /**
     * @return mixed
     */
    public function discountApplied()
    {
        return $this->discount_applied;
    }

    /**
     * @param mixed $discount_applied
     */
    public function setDiscountApplied($discount_applied): void
    {
        $this->discount_applied = $discount_applied;
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function deliveryAddress()
    {
        return $this->delivery_address;
    }

    /**
     * @param mixed $delivery_address
     */
    public function setDeliveryAddress($delivery_address): void
    {
        $this->delivery_address = $delivery_address;
    }

    /**
     * @return mixed
     */
    public function totalOrderPrice()
    {
        return $this->total_order_price;
    }

    /**
     * @param mixed $total_order_price
     */
    public function setTotalOrderPrice($total_order_price): void
    {
        $this->total_order_price = $total_order_price;
    }

    public function orderItems(): array
    {
        return $this->order_items;
    }

    public function appendOrderItems(OrderItem $order_item): void
    {
        $this->order_items[] = $order_item;
    }

    public function setOrderItems(array $order_items): void
    {
        $this->order_items = $order_items;
    }

    public function countItems()
    {
        return count($this->order_items);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }


}