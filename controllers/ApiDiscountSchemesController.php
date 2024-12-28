<?php

namespace controllers;

use controllers\ApiController;
use core\App;
use core\Request;
use http\forms\DiscountSchemeForm;
use models\orm\DiscountSchemes;
use models\orm\Properties;
use models\DiscountScheme;

class ApiDiscountSchemesController extends ApiController
{
    private DiscountSchemes $discountSchemes;

    /**
     * @param DiscountSchemes $discountSchemes
     */
    public function __construct()
    {
        $this->discountSchemes = App::resolve(DiscountSchemes::class);
    }


    public function deleteDiscountScheme()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $res = $this->discountSchemes->delete($key);

        if (!$res){
            $this->error(422, 'Error delete scheme');
        }

        $this->success('DiscountScheme deleted successfully', [
            'discount_scheme_id'=>$key
        ]);
    }

    public function addDiscountScheme()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'min_spent_amount'=>Request::post('min_spent_amount'),
            'discount_percentage'=>Request::post('discount_percentage')
        ];

        $form = new DiscountSchemeForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->discountSchemes->add(new DiscountScheme(
            -1,
            $attributes['min_spent_amount'],
            $attributes['discount_percentage']
        ));

        $this->success('Scheme added successfully');
    }

    public function updateDiscountScheme()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'min_spent_amount'=>Request::post('min_spent_amount'),
            'discount_percentage'=>Request::post('discount_percentage')
        ];

        $form = new DiscountSchemeForm($attributes, true);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->discountSchemes->update($attributes['id'], new DiscountScheme(
            $attributes['id'],
            $attributes['min_spent_amount'],
            $attributes['discount_percentage']
        ));

        $this->success('DiscountScheme update successfully', [
            'scheme'=>$this->dismount($this->discountSchemes->get($attributes['id']))
        ]);
    }

    public function getDiscountScheme()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $scheme = $this->discountSchemes->get($id);

        if (!$scheme)
            $this->error(404, "DiscountScheme not found with id: '{$id}'");

        $this->success('DiscountScheme fetch successfully', [
            'scheme'=> $this->dismount($scheme)
        ]);
    }

    public function getDiscountSchemesPagination()
    {
        if (!$this->authenticateAdmin()) return;

        $roles = $this->discountSchemes->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$roles)
            $this->error(404, "Properties fetch failed");

        echo json_encode($roles);
    }
}