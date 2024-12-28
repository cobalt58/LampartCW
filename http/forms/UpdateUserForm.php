<?php

namespace http\forms;

use core\validation\Validator;

class UpdateUserForm extends Form
{
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        if (!Validator::email($attributes['email'])){
            $this->error('email', 'Ви впевнені, що ввели правильний Email?');
        }

        if (!Validator::string($attributes['email'], 3, 100)){
            $this->error('email1', 'Email задовгий, максимум 100 симолів');
        }

        if (!Validator::string($attributes['surname'], 1, 30)){
            $this->error('name', 'Прізвище задовге, максимум 30 симолів');
        }

        if (!Validator::string($attributes['name'], 1, 30)){
            $this->error('name', 'Ім\'я задовге, максимум 30 симолів');
        }

        if (!Validator::string($attributes['middlename'], 1, 30)){
            $this->error('name', 'По батькові задовге, максимум 30 симолів');
        }

        if (!Validator::phone($attributes['phone'])){
            $this->error('phone', 'Некоректний номер телефону');
        }

        if (!Validator::string($attributes['phone'], 10, 10)){
            $this->error('phone1', 'Телефон має складатися із 10 симлолів');
        }

        if (!empty($attributes['password-old']) && !Validator::string($attributes['password-old'], 7, 100)){
            $this->error('password-old', 'Введіть старий пароль');
        }

        if (!empty($attributes['password'])){
            if (!Validator::string($attributes['password'], 7, 100)){
                $this->error('password', 'Пароль повинен бути від 7 до 100 символів');
            }

            if ($attributes['password'] != $attributes['password-confirm']){
                $this->error('password1', 'Паролі не збігаються');
            }
        }


    }
}