<?php

namespace controllers;

use core\App;
use core\Controller;
use core\Request;
use http\forms\PropertyForm;
use models\orm\Properties;
use models\Property;

class ApiPropertiesController extends ApiController
{
    private Properties $properties;

    /**
     * @param Properties $properties
     */
    public function __construct()
    {
        $this->properties = App::resolve(Properties::class);
    }


    public function deleteProperty()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $res = $this->properties->delete($key);

        if (!$res){
            $this->error(422, 'Error delete property');
        }

        $this->success('Property deleted successfully', [
            'property_id'=>$key
        ]);
    }

    public function addProperty()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'name'=>Request::post('name')
        ];

        $form = new PropertyForm($attributes);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->properties->add(new Property(
            -1,
            $attributes['name']
        ));

        $this->success('Role added successfully');
    }

    public function updateProperty()
    {
        if (!$this->authenticateAdmin()) return;

        $attributes = [
            'id'=>Request::post('id'),
            'name'=>Request::post('name')
        ];

        $form = new PropertyForm($attributes, true);

        if ($form->failed()){
            $this->error(422, 'Form validate failed', ['errors'=>$form->errors()]);
            die();
        }

        $this->properties->update($attributes['id'], new Property(
            $attributes['id'],
            $attributes['name']
        ));

        $this->success('Property update successfully', [
            'property'=>$this->dismount($this->properties->get($attributes['id']))
        ]);
    }

    public function getProperty()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $property = $this->properties->get($id);

        if (!$property)
            $this->error(404, "Property not found with id: '{$id}'");

        $this->success('Property fetch successfully', [
            'property'=> $this->dismount($property)
        ]);
    }

    public function getAll()
    {
        $property = $this->properties->get();

        $this->success('Property fetch successfully', [
            'properties'=> $this->dismount($property)
        ]);
    }

    public function getPropertiesPagination()
    {
        if (!$this->authenticateAdmin()) return;

        $roles = $this->properties->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$roles)
            $this->error(404, "Properties fetch failed");

        echo json_encode($roles);
    }
}