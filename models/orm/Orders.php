<?php

namespace models\orm;

use core\App;
use core\database\DB;
use core\database\DBModel;
use Exception;
use models\Order;
use models\OrderItem;
use models\Promotion;

class Orders extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Ordering');
    }

    /**
     * @return Order|Order[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)) {
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])) {
            $rows = $this->db->find($this->table)->select('*')->where('order_id', 'in', '(' . implode(',', $key) . ')')->rows();
        }
        if (is_numeric($key)) {
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('order_id', '=', $key)->row());
        }

        return $this->rowsToClass($rows);
    }

    public function getUsersOrders($key = null)
    {
        $rows = [];
        if (!is_numeric($key)) {
            return [];
        }

        return $this->rowsToClass($this->db->find($this->table)->select('*')->where('user_id', '=', $key)->rows());
    }

    /**
     * @param int $key
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            $this->db->delete('Order_Item')->where('order_id', '=', $key)->run();
            $this->db->delete($this->table)->where('order_id', '=', $key)->run();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function process($key)
    {
        $this->db->update($this->table)
            ->fields(['status'])
            ->value(['Опрацьоване'])
            ->where('order_id', '=', $key)
            ->run();
        /** @var Order $order */
        $order = $this->get($key);
        /** @var Users $users */
        $users = App::resolve(Users::class);
        $user = $users->get($order->userId());
        $this->db->update('User')->fields(['total_spent_amount'])->value([$user->totalSpentAmount() + $order->totalOrderPrice()])->where('user_id','=',$order->userId())->run();
    }
    /**
     * @param int $key
     * @param Promotion $data
     * @return void
     */

    /**
     * @return array{draw: int, recordsTotal: int, recordsFiltered: mixed, data: array}
     */
    public function pagination($draw, $search, $order, $length, $start): array
    {
        $recordsTotal = $this->db->find($this->table)->select('count(*) as `count`')
            ->innerJoin('User', [['field' => "{$this->table}.user_id", 'condition' => '=', 'value' => 'User.user_id']])
            ->row()['count'];
        $data = [];

        $query = $this->db->find($this->table)->select('*')
            ->innerJoin('User', [['field' => "{$this->table}.user_id", 'condition' => '=', 'value' => 'User.user_id']]);

        $columns = array(
            0 => '`name`',
            1 => '`phone`',
            2 => '`email`',
            3 => '`surname`',
            4 => 'status',
            5 => 'order_date',
            6 => 'total_order_price',
        );

        if (isset($search['value'])) {
            $searchValue = $search['value'];
            if (!empty($searchValue)) $query->where('`name`', 'like', "$searchValue%", 'OR');
            if (!empty($searchValue)) $query->where('`phone`', 'like', "$searchValue%", 'OR');
            if (!empty($searchValue)) $query->where('`email`', 'like', "$searchValue%", 'OR');
            if (!empty($searchValue)) $query->where('`surname`', 'like', "$searchValue%", 'OR');
            if (!empty($searchValue)) $query->where('`status`', 'like', "$searchValue%", 'OR');
            if (is_numeric($searchValue)) $query->where('total_order_price', '=', $searchValue, 'OR');
        }

        if (isset($order)) {
            $column_name = $order[0]['column'];
            $dir = $order[0]['dir'];
            $query->orderBy("{$columns[$column_name]} $dir");
        } else {
            $query->orderBy("{$columns[0]} desc");
        }

        $recordsFiltered = count($query->rows());

        if ($length != -1) {
            $query->limit($length)->offset($start);
        }

        $rows = $query->rows();

        foreach ($rows as $row) {
            $data[] = [
                $row['lastname'] . " " . $row['name'] . " " . $row['middlename'],
                $row['order_date'],
                $row['delivery_address'],
                $row['total_order_price'],
                $row['status'],
                $row['order_id']
            ];
        }
        return [
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    protected function rowToClass($row): Order
    {
        /** @var Users $users */
        $users = App::resolve(Users::class);
        $order = new Order(
            $row['order_id'],
            $row['user_id'],
            $row['order_date'],
            $row['total_amount'],
            $row['final_amount'],
            $row['status'],
            $row['delivery_address'],
            $row['total_order_price'],
            $users->get($row['user_id'])
        );

        $orderItems = $this->db->find('Order_Item')
            ->select('*')
            ->where('order_id', '=', $row['order_id'])
            ->rows();

        /** @var Products $products */
        $products = App::resolve(Products::class);

        foreach ($orderItems as $orderItem) {
            $order->appendOrderItems(new OrderItem(
                $orderItem['order_item_id'],
                $orderItem['order_id'],
                $orderItem['product_id'],
                $orderItem['quantity'],
                $orderItem['price_at_order_time'],
                $products->get($orderItem['product_id'])
            ));
        }

        return $order;
    }
}