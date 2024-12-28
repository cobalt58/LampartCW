<?php

namespace http\forms;

use core\App;
use core\validation\Validator;
use http\forms\Form;
use models\orm\Products;
use models\orm\Properties;

class ProductForm extends Form
{

    public function __construct($attributes, $update = false)
    {
        parent::__construct($attributes);

        if (!Validator::string($attributes['name'], 3, 100)){
            $this->error('name', 'Назва повинна бути від 3 до 100 символів.');
        }

        if (!Validator::string($attributes['description'], 3, 200)){
            $this->error('name', 'Опис повинен бути від 3 до 200 символів.');
        }

        if (empty($attributes['categories'])){
            $this->error('categories', 'Продукт повинен містити хочаб одну категорію.');
        }

        /** @var Products $products */
        $products = App::resolve(Products::class);

        $product = $update
            ? $products->findWithParams([['name', 'like', $attributes['name']], ['product_id', '<>', $attributes['id']]])
            : $products->findOne('name', 'like', $attributes['name']);

        if (!empty($product)){
            $this->error('name1', 'Продукт з такою назвою вже існує');
        }
    }
}