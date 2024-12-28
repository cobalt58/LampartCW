<?php

namespace models;

class DiscountScheme
{
    private $id;
    private $min_spent_amount;
    private $discount_percentage;

    /**
     * @param $id
     * @param $min_spent_amount
     * @param $discount_percentage
     */
    public function __construct($id, $min_spent_amount, $discount_percentage)
    {
        $this->id = $id;
        $this->min_spent_amount = $min_spent_amount;
        $this->discount_percentage = $discount_percentage;
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
    public function minSpentAmount()
    {
        return $this->min_spent_amount;
    }

    /**
     * @param mixed $min_spent_amount
     */
    public function setMinSpentAmount($min_spent_amount): void
    {
        $this->min_spent_amount = $min_spent_amount;
    }

    /**
     * @return mixed
     */
    public function discountPercentage()
    {
        return $this->discount_percentage;
    }

    /**
     * @param mixed $discount_percentage
     */
    public function setDiscountPercentage($discount_percentage): void
    {
        $this->discount_percentage = $discount_percentage;
    }

}