<?php

namespace models;

use core\App;
use DateTime;
use models\orm\Products;

class Promotion
{
    private int $id;
    private int $product_id;
    private int $discount_percentage;
    private DateTime $start_date;
    private DateTime $end_date;
    private ?Product $product;
    /**
     * @param int $id
     * @param int $product_id
     * @param int $discount_percentage
     * @param DateTime $start_date
     * @param DateTime $end_date
     */
    public function __construct(int $id, int $product_id, int $discount_percentage, DateTime $start_date, DateTime $end_date)
    {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->discount_percentage = $discount_percentage;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->product = new Product(-1, 'TEST', 'TEST', '-1', '-1');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function productId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function discountPercentage(): int
    {
        return $this->discount_percentage;
    }

    public function setDiscountPercentage(int $discount_percentage): void
    {
        $this->discount_percentage = $discount_percentage;
    }

    public function startDate(): DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(DateTime $start_date): void
    {
        $this->start_date = $start_date;
    }

    public function endDate(): DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(DateTime $end_date): void
    {
        $this->end_date = $end_date;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }


}