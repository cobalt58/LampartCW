<?php

namespace models;

use core\App;
use models\orm\Promotions;

class Product
{
    private int $id;
    private string $name;
    private string $description;
    private $price;
    private $priceWithDiscount;
    private ?array $promotions;
    private string $quantity;
    private ?array $categories;
    private ?array $properties;
    private ?array $images;
    /**
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $price
     * @param ?array $promotions
     * @param string $quantity
     * @param ?array $categories
     * @param ?array $properties
     */
    public function __construct(int $id, string $name, string $description, string $price, string $quantity, array $promotions = null, array $categories = null, array $properties = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->promotions = $promotions;
        $this->quantity = $quantity;
        $this->categories = $categories;
        $this->properties = $properties;
        $this->images = $id != null && $id > 0 ? getImagesFromDirectory(base_path("products-images".DIRECTORY_SEPARATOR."product-{$this->id}")) : null;

        if (is_array($this->images())){
            $n_ar = [];
            foreach ($this->images() as $image) {
                $n_ar[] = "products-images/product-{$id}/{$image}";
            }
            $this->images = $n_ar;
        }

        if ($id>0){
            /** @var Promotions $promotions */
            $promotions = App::resolve(Promotions::class);
            /** @var Promotion $p */
            $p = $promotions->findOne('product_id', '=', $id);
            if ($p){
                $this->priceWithDiscount = $this->price - $this->price * $p->discountPercentage()/100;
            }else{
                $this->priceWithDiscount = $this->price;
            }
        }else{
            $this->priceWithDiscount = $this->price;
        }

    }

    public function images(): ?array
    {
        return $this->images;
    }



    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function price()
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function promotions(): ?array
    {
        return $this->promotions;
    }

    public function setPromotions(?array $promotions): void
    {
        $this->promotions = $promotions;
    }

    public function quantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function categories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): void
    {
        $this->categories = $categories;
    }

    public function properties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(?array $properties): void
    {
        $this->properties = $properties;
    }

    public function priceWithDiscount()
    {
        return $this->priceWithDiscount;
    }


}