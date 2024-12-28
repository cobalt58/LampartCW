<?php

namespace core\auth;

use core\App;
use core\Session;
use models\orm\Users;
use models\User;
use models\UserHash;

class Authenticator
{
    protected array $errors = [];

    public function attempt($email, $password)
    {
        /**
         * @var Users $users
         */
        $users = App::resolve(Users::class);
        /**
         * @var User $user
         */
        $user = $users->findOne('email', 'like', $email);

        if (is_null($user)){
            $this->errors['email'] = 'Користувача з таким Email не знайдено!';
        }

        if ($user && $user->role() == 'ban'){
            $this->errors['ban'] = 'Цей акаунт заблоковано адміністрацією!';
        }

        if ($user && !password_verify($password, $user->hash())){
            $this->errors['password'] = 'Введені дані не є дійсними';
        }

        if (!$this->failed()){
            $this->login([
                'email' => $user->email(),
                'phone' => $user->phone(),
                'auth' => true,
                'role' => $user->role(),
                'id' => $user->id()
            ]);
        }
    }

    public function failed(): bool
    {
        return (bool)count($this->errors);
    }

    protected function login($user)
    {
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
    }

    public static function logout()
    {
        Session::destroy();
        redirect('/');
    }

    public function errors(): array
    {
        return $this->errors;
    }

}