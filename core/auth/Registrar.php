<?php

namespace core\auth;

use core\App;
use core\Uploader;
use models\orm\Users;
use models\Role;
use models\User;
use models\UserHash;

class Registrar
{
    protected array $errors = [];

    public function attempt(User $new_user, $files = null): void
    {
        /**
         * @var Users $users
         */
        $users = App::resolve(Users::class);

        if ($users->findOne('phone', 'like', $new_user->phone())){
            $this->errors['phone'] = 'Телефон вже зайнятий!';
        }
        /**
         * @var Users $users
         */
        $users = App::resolve(Users::class);
        if ($users->findOne('email', 'like', $new_user->email())){
            $this->errors['email'] = 'Email вже використовується!';
        }

        if (!$this->failed()){
            $new_user->setHash(password_hash($new_user->hash(), PASSWORD_DEFAULT));


            $new_id = $users->add($new_user);

            if (!empty($files['media']['name'])){
                $new_user->setAvatar(Uploader::uploadSingle($files, 'media', base_path('users-avatars'), $new_id));
            }


        }

    }

    public function failed(): bool
    {
        return (bool)count($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}