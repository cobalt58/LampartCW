<?php

namespace models;

class ProductProperty
{
    private int $product_id;
    private int $property_id;
    private $property_value;

    /**
     * @param int $product_id
     * @param int $property_id
     * @param mixed $property_value
     */
    public function __construct(int $product_id, int $property_id, $property_value)
    {
        $this->product_id = $product_id;
        $this->property_id = $property_id;
        $this->property_value = $property_value;
    }

    public function productId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function propertyId(): int
    {
        return $this->property_id;
    }

    public function setPropertyId(int $property_id): void
    {
        $this->property_id = $property_id;
    }

    public function propertyValue()
    {
        return $this->property_value;
    }

    public function setPropertyValue(mixed $property_value): void
    {
        $this->property_value = $property_value;
    }


    public function __serialize(): array
    {
        return [
            'product_id'=>$this->product_id,
            'property_id'=>$this->property_id,
            'value'=>$this->property_value
        ];
    }

}