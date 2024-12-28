<?php

namespace controllers;

use controllers\ApiController;
use core\App;
use core\Request;
use http\forms\ProductForm;
use http\forms\PropertyForm;
use models\orm\Products;
use models\orm\Properties;
use models\Product;
use models\Property;

class ApiProductsController extends ApiController
{
    private Products $products;

    public function __construct()
    {
        $this->products = App::resolve(Products::class);
    }

    public function deleteProduct()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $res = $this->products->delete($key);

        if (!$res){
            $this->error(422, 'Error delete property');
        }

        $this->success('Product deleted successfully', [
            'product_id'=>$key
        ]);
    }

    public function addProduct()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'name'=>Request::post('name'),
            'description'=>Request::post('description'),
            'price'=>Request::post('price'),
            'quantity'=>Request::post('quantity'),
            'categories'=>Request::post('categories'),
            'properties'=>Request::post('properties')
        ];

        $form = new ProductForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->products->add(new Product(
            -1,
            $attributes['name'],
            $attributes['description'],
            $attributes['price'],
            $attributes['quantity'],
            null,
            $attributes['categories'],
            $attributes['properties'],
        ), $_FILES);

        $this->success('Product added successfully');
    }
    public function getAll()
    {
        $products = $this->products->get();

        $this->success('Products fetch successfully', [
            'products'=> $this->dismount($products)
        ]);
    }
    public function updateProduct()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'name'=>Request::post('name'),
            'description'=>Request::post('description'),
            'price'=>Request::post('price'),
            'quantity'=>Request::post('quantity'),
            'categories'=>Request::post('categories'),
            'properties'=>Request::post('properties')
        ];

        $form = new ProductForm($attributes, true);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->products->update($attributes['id'], new Product(
            $attributes['id'],
            $attributes['name'],
            $attributes['description'],
            $attributes['price'],
            $attributes['quantity'],
            null,
            $attributes['categories'],
            $attributes['properties'],
        ), $_FILES);

        $this->success('Product update successfully', [
            'product'=>$this->dismount($this->products->get($attributes['id']))
        ]);
    }

    public function getProduct()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $product = $this->products->get($id);

        if (!$product)
            $this->error(404, "Product not found with id: '{$id}'");

        $this->success('Product fetch successfully', [
            'product'=> $this->dismount($product)
        ]);
    }

    public function deleteProductImage()
    {
        if (!$this->authenticateAdmin()) return;
        $image = Request::post('image');

        if (!$image){
            $this->error(422, 'Image name is required');
            die();
        }

        unlink(base_path($image));

        $this->success('Image deleted successfully');
    }

    public function getProductsPagination()
    {

        if (!$this->authenticateAdmin()) return;

        $roles = $this->products->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$roles)
            $this->error(404, "Properties fetch failed");

        echo json_encode($roles);
    }

    public function getProductsNeoPagination()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $places = $this->products->neoPagination($data['draw'], $data['search'], $data['order'], $data['length'], $data['start']);

        if (!$places)
            $this->error(404, "Places fetch failed");

        header('Content-Type: application/json');
        echo json_encode($this->dismount($places));
    }
}