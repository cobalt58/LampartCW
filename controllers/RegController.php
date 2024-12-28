<?php

namespace controllers;

use core\auth\Authenticator;
use core\auth\Registrar;
use core\Controller;
use core\Request;
use http\forms\LoginForm;
use http\forms\RegForm;
use models\User;

class RegController extends Controller
{
    public function index()
    {
        $this->view('auth/sign-up');
    }

    public function registration()
    {
        Request::GetAndRedirect('/');
        $attributes = [
            'surname'=>Request::post('surname'),
            'name'=>Request::post('name'),
            'middlename'=>Request::post('middlename'),
            'phone'=>Request::post('phone'),
            'email'=>Request::post('email'),
            'password'=>Request::post('password'),
            'password-confirm'=>Request::post('password-confirm'),
        ];

        $form = RegForm::validate($attributes);
        $registrar = new Registrar();

        $registrar->attempt(new User(
            -1,
            $attributes['surname'],
            $attributes['name'],
            $attributes['middlename'],
            $attributes['email'],
            $attributes['phone'],
            $attributes['password'],
            "user"
        ));


        if ($registrar->failed()){
            $form->appendErrors($registrar->errors())->throw();
        }

        (new Authenticator())->attempt($attributes['email'], $attributes['password']);

        redirect('/');

    }
}