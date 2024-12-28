<?php

namespace http\forms;

use core\App;
use core\validation\Validator;
use http\forms\Form;
use models\orm\Categories;
use models\orm\Properties;

class PropertyForm extends Form
{
    public function __construct($attributes, $update = false)
    {
        parent::__construct($attributes);

        if (!Validator::string($attributes['name'], 3, 50)){
            $this->error('name', 'Назва повинна бути від 3 до 50 символів.');
        }

        /** @var Properties $properties */
        $properties = App::resolve(Properties::class);

        $property = $update
            ? $properties->findWithParams([['name', 'like', $attributes['name']], ['property_id', '<>', $attributes['id']]])
            : $properties->findOne('name', 'like', $attributes['name']);

        if (!empty($property)){
            $this->error('name1', 'Характеристика з такою назвою вже існує');
        }
    }
}