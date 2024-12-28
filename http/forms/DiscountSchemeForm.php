<?php

namespace http\forms;

use core\App;
use models\orm\DiscountSchemes;
class DiscountSchemeForm extends Form
{
    public function __construct($attributes, $update = false)
    {
        parent::__construct($attributes);

        /** @var DiscountSchemes $schemes*/
        $schemes = App::resolve(DiscountSchemes::class);
        $scheme = $update
            ? $schemes->findWithParams([['min_spent_amount', '=', $attributes['min_spent_amount']], ['discount_scheme_id', '<>', $attributes['id']]])
            : $schemes->findOne('min_spent_amount', '=', $attributes['min_spent_amount']);

        if (!empty($scheme)){
            $this->error('scheme', 'Така мінімальна сума витрати для накопичувальної знижки вже є.');
        }

        /** @var DiscountSchemes $schemes*/
        $schemes = App::resolve(DiscountSchemes::class);
        $scheme = $update
            ? $schemes->findWithParams([['discount_percentage', '=', $attributes['discount_percentage']], ['discount_scheme_id', '<>', $attributes['id']]])
            : $schemes->findOne('discount_percentage', '=', $attributes['discount_percentage']);

        if (!empty($scheme)){
            $this->error('scheme', 'Такий процент знижки вже є.');
        }
    }
}