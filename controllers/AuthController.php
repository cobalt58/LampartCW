<?php

namespace controllers;

use core\auth\Authenticator;
use core\Controller;
use core\Request;
use http\forms\LoginForm;

class AuthController extends Controller
{
    public function index()
    {
        $this->view('auth/sign-in');
    }

    public function auth()
    {
        $attributes = [
            'email'=>Request::post('email'),
            'password'=>Request::post('password'),
        ];

        $form = LoginForm::validate($attributes);
        $authenticator = new Authenticator();

        $authenticator->attempt($attributes['email'], $attributes['password']);

        if ($authenticator->failed()){
            $form->appendErrors($authenticator->errors())->throw();
        }

        redirect('/');
    }
}