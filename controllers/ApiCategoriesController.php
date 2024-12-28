<?php

namespace controllers;

use controllers\ApiController;
use core\App;
use core\Request;
use http\forms\CategoryForm;
use models\Category;
use models\orm\Categories;

class ApiCategoriesController extends ApiController
{
    protected Categories $categories;

    public function __construct()
    {
        $this->categories = App::resolve(Categories::class);
    }

    public function getTree()
    {
        $search = Request::post('search');
        $search = empty($search) ? null : $search;

        $this->success('Successfully fetched categories tree', [
            'tree'=>$this->dismount($this->categories->tree($search))
        ]);
    }

    public function getCategory()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(500, 'Id is required');
        }

        $this->success('Category fetched successfully', [
            'category'=>$this->dismount($this->categories->get($key))
        ]);
    }

    public function addCategory()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'title'=>Request::post('title'),
            'parent'=>Request::post('parent-category') == 'null' ? null : Request::post('parent-category'),
        ];

        $form = new CategoryForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->categories->add(new Category(
            0,
            $attributes['title'],
            $attributes['parent']
        ));

        $addedCategory = $this->categories->findOne('name', '=', $attributes['title']);

        $this->success('Category added successfully', [
            'category'=>$this->dismount($addedCategory)
        ]);
    }

    public function updateCategory()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'title'=>Request::post('title'),
            'parent'=>Request::post('parent-category') == 'null' ? null : Request::post('parent-category'),
        ];

        $form = new CategoryForm($attributes, true);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->categories->update($attributes['id'], new Category(
            $attributes['id'],
            $attributes['title'],
            $attributes['parent']
        ));

        $this->success('Category update successfully', [
            'category'=>$this->dismount(
                $this->categories->get($attributes['id'])
            )
        ]);
    }

    public function deleteCategory()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
            die();
        }

        $res = $this->categories->delete($key);

        if (!$res){
            $this->error(422, 'Error delete category');
            die();
        }

        $this->success('Category deleted successfully', [
            'category_id'=>$key
        ]);
    }

    public function search()
    {
        if (!$this->authenticateAdmin()) return;

        $search = Request::post('search');

        if (!$search){
            $this->error('422', 'Search value s required');
        }

        $this->success('Successfully fetched categories tree', [
            'tree'=>$this->dismount($this->categories->tree($search))
        ]);
    }
}