<?php

namespace http\forms;

use core\App;
use core\validation\Validator;
use models\orm\Categories;


class CategoryForm extends Form
{
    public function __construct($attributes, $update = false)
    {
        parent::__construct($attributes);

        if (!Validator::string($attributes['title'], 3, 40)){
            $this->error('name', 'Назва повинена бути від 3 до 40 символів');
        }

        /** @var Categories $roles */
        $categories = App::resolve(Categories::class);

        $category = $update
            ? $categories->findWithParams([['name', 'like', $attributes['title']], ['category_id', '<>', $attributes['id']]])
            : $categories->findOne('name', 'like', $attributes['title']);

        if (!empty($category)){
            $this->error('name1', 'Категорія з такою назвою вже існує');
        }
    }

}