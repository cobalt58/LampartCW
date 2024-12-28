<?php

namespace core\auth;

use core\App;
use core\Uploader;
use models\orm\Users;
use models\Role;
use models\User;
use models\UserHash;

class UserUpdater
{
    protected array $errors = [];

    public function attempt(User $new_user, $oldPassword, $files=null)
    {

        /**
         * @var Users $users
         */
        $users = App::resolve(Users::class);
        if (!empty($users->findWithParams([['phone', 'like', $new_user->phone()], ['user_id', '<>', $new_user->id()]]))){
            $this->errors['phone'] = 'Телефон вже зайнятий!';
        }
        /**
         * @var Users $users
         */
        $users = App::resolve(Users::class);
        if (!empty($users->findWithParams([['email', 'like', $new_user->email()], ['user_id', '<>', $new_user->id()]]))){
            $this->errors['email'] = 'Email вже використовується!';
        }

        $userToUpdate = $users->get($new_user->id());

        if (!empty($oldPassword)){
            if (!password_verify($oldPassword, $userToUpdate->hash()))
                $this->errors['password3'] = 'Невірний старий пароль';
        }

        if (!$this->failed()){


            if (!empty($password))
                $new_user->setHash(
                    password_hash($password, PASSWORD_DEFAULT),
                );
            else
                $new_user->setHash($userToUpdate->hash());

            if (!empty($files['media']['name'])){
                $new_user->setAvatar(Uploader::uploadSingle($files, 'media', base_path('users-avatars'), $userToUpdate->id()));
            }

            $users->update($userToUpdate->id(), $new_user);
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