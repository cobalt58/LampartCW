<?php

namespace controllers;

use core\App;
use core\Controller;
use core\database\DB;
use core\Request;
use models\Category;
use models\orm\Categories;
use models\orm\Products;

class ShopController extends Controller
{
    public function index()
    {
        $this->view('shop/index');
    }

    public function product()
    {
        $id = intval(App::route()->params()[0]);
        if (!$id)
            redirect('/shop');

        $uid = App::user()['id'];

        /** @var Products $products */
        $products = App::resolve(Products::class);
        $product = $products->get($id);


        $this->view('shop/product', [
            'product'=>$product,
            'uid'=>App::user()['id'] ?? -1,
            'pid'=>$id,
        ]);
    }

    public function categories()
    {
        $pid = Request::post('pid') == 'null'? null : Request::post('pid');

        /* @var Categories $categories */
        $categories = App::resolve(Categories::class);

        $rows = $categories->find('parent_category_id', ((is_null($pid))?'is null':'='), $pid);

        $category = is_null($pid)? null : $categories->get($pid);

        header('Content-Type: application/json');
        echo json_encode(
            [
                'category'=>dismount($category),
                'children'=>dismount($rows)
            ]
        );
    }

    public function minMaxPrice()
    {
        /** @var DB $db */
        $db = App::resolve(DB::class);
        $row = $db->find('productwithdiscount')->select(['min(discounted_price) as min','max(discounted_price) as max'])->row();

        header('Content-Type: application/json');
        echo json_encode(
            [
                'min'=>$row['min'],
                'max'=>$row['max']
            ]
        );
    }

    protected function categoriesChild()
    {
        /* @var Categories $categories */
        $categories = App::resolve(Categories::class);
        $id = Request::get('id');
        $base_cats = [];
        /* @var Category[] $rows */
        $rows = $categories->find('category_parent', (($id == null)?'is null':'='), $id);

        foreach ($rows as $row){
            $base_cats[$row->title()] = [intval($row->id())];
            $stack = [$row->id()];

            while (!empty($stack)){
                $id = array_pop($stack);
                $nrows = $categories->find('category_parent', '=', $id);
                foreach ($nrows as $nrow){
                    $base_cats[$row->title()][] = intval($nrow->id());
                    $stack[] = intval($nrow->id());
                }
            }
        }

        echo json_encode($base_cats);
    }
}