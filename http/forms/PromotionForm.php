<?php

namespace http\forms;

use core\App;
use http\forms\Form;
use models\orm\Promotions;

class PromotionForm extends Form
{
    public function __construct($attributes, $update = false)
    {
        parent::__construct($attributes);

        /** @var Promotions $promotions*/
        $promotions = App::resolve(Promotions::class);
        $promotion = $update
            ? $promotions->findWithParams([['product_id', '=', $attributes['product_id']], ['promotion_id', '<>', $attributes['id']]])
            : $promotions->findOne('product_id', '=', $attributes['product_id']);

        if (!empty($promotion)){
            $this->error('product', 'Знижка на цей товар вже є.');
        }
    }
}