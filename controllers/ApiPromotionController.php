<?php

namespace controllers;

use controllers\ApiController;
use core\App;
use core\Request;
use DateTime;
use http\forms\PromotionForm;
use models\orm\Products;
use models\orm\Promotions;
use models\orm\Properties;
use models\Promotion;

class ApiPromotionController extends ApiController
{
    private Promotions $promotions;

    /**
     * @param Promotions $promotions
     */
    public function __construct()
    {
        $this->promotions = App::resolve(Promotions::class);
    }


    public function deletePromotion()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $res = $this->promotions->delete($key);

        if (!$res){
            $this->error(422, 'Error delete promotions');
        }

        $this->success('Promotion deleted successfully', [
            'promotion_id'=>$key
        ]);
    }

    public function addPromotion()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'product_id'=>Request::post('product_id'),
            'discount_percentage'=>Request::post('discount_percentage'),
            'start_date'=>Request::post('start_date'),
            'end_date'=>Request::post('end_date')
        ];

        $form = new PromotionForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->promotions->add(new Promotion(
            -1,
            $attributes['product_id'],
            $attributes['discount_percentage'],
            new DateTime($attributes['start_date']),
            new DateTime($attributes['end_date']),
        ));

        $this->success('Role added successfully');
    }

    public function updatePromotion()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'product_id'=>Request::post('product_id'),
            'discount_percentage'=>Request::post('discount_percentage'),
            'start_date'=>Request::post('start_date'),
            'end_date'=>Request::post('end_date')
        ];

        $form = new PromotionForm($attributes, true);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->promotions->update($attributes['id'], new Promotion(
            $attributes['id'],
            $attributes['product_id'],
            $attributes['discount_percentage'],
            new DateTime($attributes['start_date']),
            new DateTime($attributes['end_date']),
        ));



        $promotion = $this->promotions->get($attributes['id']);
        /** @var Products $products */
        $products = App::resolve(Products::class);
        $promotion->setProduct($products->get($promotion->productId()));

        $this->success('Promotion update successfully', [
            'promotion'=>$this->dismount($promotion)
        ]);
    }

    public function getPromotion()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $promotion = $this->promotions->get($id);
        /** @var Products $products */
        $products = App::resolve(Products::class);
        $promotion->setProduct($products->get($promotion->productId()));

        if (!$promotion)
            $this->error(404, "Promotion not found with id: '{$id}'");

        $this->success('Promotion fetch successfully', [
            'promotion'=> $this->dismount($promotion)
        ]);
    }

    public function getPromotionsPagination()
    {
        if (!$this->authenticateAdmin()) return;

        $roles = $this->promotions->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$roles)
            $this->error(404, "Promotions fetch failed");

        echo json_encode($roles);
    }
}