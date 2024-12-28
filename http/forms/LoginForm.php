<?php

namespace http\forms;

use core\validation\ValidationException;
use core\validation\Validator;

class LoginForm extends Form
{
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        if (!Validator::email($attributes['email'])){
            $this->error('email', 'Ви впевнені, що ввели правильний Email?');
        }
    }
}