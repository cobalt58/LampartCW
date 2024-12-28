<?php

namespace controllers;

use core\App;
use core\Controller;

class AdminController extends Controller
{
    public function index()
    {
        //$this->view('admin/index');
        App::setRoute('/admin/products');
        $this->view('admin/products/index');
    }
    public function products()
    {
        App::setRoute('/admin/products');
        $this->view('admin/products/index');
    }

    public function promotions()
    {
        App::setRoute('/admin/promotions');
        $this->view('admin/promotions/index');
    }

    public function orders()
    {
        App::setRoute('/admin/orders');
        $this->view('admin/orders/index');
    }

    public function discountSchemes()
    {
        App::setRoute('/admin/discountSchemes');
        $this->view('admin/discountSchemes/index');
    }

    public function properties()
    {
        App::router()->setRoute('/admin/properties');
        $this->view('admin/properties/index');
    }

    public function categories()
    {
        App::router()->setRoute('/admin/categories');
        $this->view('admin/categories/index');
    }
    public function users()
    {
        App::router()->setRoute('/admin/users');
        $this->view('admin/users/index');
    }
}