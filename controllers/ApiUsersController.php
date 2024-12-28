<?php

namespace controllers;

use core\App;
use core\auth\Registrar;
use core\auth\UserUpdater;
use core\Request;
use http\forms\RegForm;
use http\forms\UpdateUserForm;
use models\orm\Users;
use models\User;

class ApiUsersController extends ApiController
{

    protected Users $users;

    public function __construct()
    {
        $this->users = App::resolve(Users::class);
    }

    public function index()
    {

    }

    public function banUser()
    {
        if (!$this::authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
            die();
        }

        $this->users->ban($key);

        $this->success('User banned successfully', [
            'user'=>$this->dismount($this->users->get($key))
        ]);
    }

    public function unbanUser()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
            die();
        }

        $this->users->unban($key);

        $this->success('User unbanned successfully', [
            'user'=>$this->dismount($this->users->get($key))
        ]);
    }

    public function deleteUser()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');


        if (!$key){
            $this->error(422, 'Id is required');
            die();
        }

        $userToDelete = $this->users->get($key);

        $res = $this->users->delete($key);

        if (!$res){
            $this->error(422, 'Error delete user');
            die();
        }

        if ($userToDelete->avatar()) unlink(base_path($userToDelete->avatar()));

        $this->success('User deleted successfully', [
            'user_id'=>$key
        ]);
    }

    public function addUser()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'surname'=>Request::post('surname'),
            'name'=>Request::post('name'),
            'middlename'=>Request::post('middlename'),
            'phone'=>Request::post('phone'),
            'email'=>Request::post('email'),
            'password'=>Request::post('password'),
            'password-confirm'=>Request::post('password-confirm'),
            'role'=>Request::post('role')
        ];

        $form = new RegForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $registrar = new Registrar();
        $registrar->attempt(
            new User(
                -1,
                $attributes['surname'],
                $attributes['name'],
                $attributes['middlename'],
                $attributes['email'],
                $attributes['phone'],
                $attributes['password'],
                $attributes['role']
            ),
            !empty($_FILES['media']['name']) ? $_FILES : null
        );

        if ($registrar->failed()){
            $this->error(500, 'Adding user failed', ['errors'=>$registrar->errors()]);
            die();
        }

        $this->success('User added successfully');
    }

    public function updateUser()
    {
        if (!$this->authenticate()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'surname'=>Request::post('surname'),
            'name'=>Request::post('name'),
            'middlename'=>Request::post('middlename'),
            'phone'=>Request::post('phone'),
            'email'=>Request::post('email'),
            'password-old'=>empty(Request::post('password-old')) ? null : Request::post('password-old'),
            'password'=>empty(Request::post('password')) ? null : Request::post('password'),
            'password-confirm'=>empty(Request::post('password-confirm')) ? null : Request::post('password-confirm'),
            'role'=>Request::post('role')
        ];

        $form = new UpdateUserForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $updater = new UserUpdater();
        $updater->attempt(
            new User(
                $attributes['id'],
                $attributes['surname'],
                $attributes['name'],
                $attributes['middlename'],
                $attributes['email'],
                $attributes['phone'],
                $attributes['password'],
                $attributes['role']
            ),
            $attributes['password-old'] ?? null,
            !empty($_FILES['media']['name']) ? $_FILES : null
        );

        if ($updater->failed()){
            $this->error(500, 'Updating user failed', ['errors'=>$updater->errors()]);
            die();
        }

        $this->success('User update successfully', [
            'user'=>$this->dismount($this->users->get($attributes['id']))
        ]);
    }

    public function getUsers()
    {
        if (!App::isAdmin())
            $this->error(500, 'Access denied');


        $users = $this->users->get();

        if (!$users)
            $this->error(404, "Users fetch failed");

        $this->success('Users fetch successfully', [
            'users'=> $this->dismount($users)
        ]);
    }

    public function getUsersPagination()
    {
        if (!App::isAdmin())
            $this->error(500, 'Access denied');

        $users = $this->users->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$users)
            $this->error(404, "Users fetch failed");

        echo json_encode($users);
    }

    public function getActiveUsers()
    {
        $this->authenticateAdmin();

        $list = $this->users->getActiveUsers(10);

        if (!$list)
            $this->error(500, 'Error fetching active users');

        $this->success('Active users fetched successfully', [
            'list'=>$list
        ]);

    }

    public function getUser()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $user = $this->users->get($id);

        if (!$user)
            $this->error(404, "User not found with id: '{$id}'");

        $this->success('User fetch successfully', [
            'user'=> $this->dismount($user)
        ]);
    }

    private function authenticate(): bool
    {
        if (!App::isUser())
            $this->error(500, 'Access denied');

        if (App::user()['id'] !== Request::post('id') && !App::isAdmin())
            $this->error(500, 'You are not allow to do this');

        return true;
    }
}