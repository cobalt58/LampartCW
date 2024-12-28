<?php

namespace models;

class ProductCategory
{
    private int $product_id;
    private int $category_id;
    private string $name;

    /**
     * @param int $product_id
     * @param int $category_id
     * @param string $name
     */
    public function __construct(int $product_id, int $category_id, string $name)
    {
        $this->product_id = $product_id;
        $this->category_id = $category_id;
        $this->name = $name;
    }

    public function productId(): int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function categoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __serialize(): array
    {
        return [
            'product_id'=>$this->product_id,
            'category_id'=>$this->category_id,
            'name'=>$this->name
        ];
    }

}