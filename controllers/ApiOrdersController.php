<?php

namespace controllers;

use core\App;
use core\Request;
use http\forms\ProductForm;
use models\orm\Carts;
use models\orm\Orders;
use models\Product;

class ApiOrdersController extends ApiController
{
    protected Orders $orders;

    public function __construct()
    {
        $this->orders = App::resolve(Orders::class);
    }

    public function deleteOrder()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $res = $this->orders->delete($key);

        if (!$res){
            $this->error(422, 'Error delete property');
        }

        $this->success('Product deleted successfully', [
            'order_id'=>$key
        ]);
    }

    public function process()
    {
        if (!$this->authenticateAdmin()) return;

        $key = Request::post('id');

        if (!$key){
            $this->error(422, 'Id is required');
        }

        $this->orders->process($key);

        $this->success('Order update successfully', [
            'order'=>$this->dismount($this->orders->get($key))
        ]);
    }

    public function getOrder()
    {
        $id = Request::post('id');
        if (!$id)
            $this->error('422', 'Id is required');

        $product = $this->orders->get($id);

        if (!$product)
            $this->error(404, "Order not found with id: '{$id}'");

        $this->success('Order fetch successfully', [
            'order'=> $this->dismount($product)
        ]);
    }

    public function getOrdersPagination()
    {

        if (!$this->authenticateAdmin()) return;

        $roles = $this->orders->pagination($_POST['draw'], $_POST['search'], $_POST['order'], $_POST['length'], $_POST['start']);

        if (!$roles)
            $this->error(404, "Orders fetch failed");

        echo json_encode($roles);
    }
}