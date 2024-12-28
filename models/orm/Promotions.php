<?php

namespace models\orm;

use core\database\DB;
use core\database\DBModel;
use DateTime;
use Exception;
use models\Promotion;

class Promotions extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Promotion');
    }

    /**
     * @param Promotion $data
     * @return void
     */
    public function add($data)
    {
        $this->db->insert($this->table)
            ->fields(['product_id', 'discount_percentage', 'start_date', 'end_date'])
            ->value([
                $data->productId(),
                $data->discountPercentage(),
                $data->startDate()->format('Y-m-d'),
                $data->endDate()->format('Y-m-d')
            ])
            ->run();
    }

    /**
     * @return Promotion|Promotion[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])){
            $rows = $this->db->find($this->table)->select('*')->where('promotion_id','in', '('. implode(',',$key) .')')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('promotion_id','=',$key)->row());
        }

        return $this->rowsToClass($rows);
    }

    /**
     * @param $key
     * @return array[ProductProperty]
     */
    public function getProductPromotions($key = null): array
    {
        if (is_null($key)){
            return [];
        }
        if (is_numeric($key)){
            return $this->rowsToClass(
                $this->db->find($this->table)
                    ->select('*')
                    ->where('product_id','=',$key)
                    ->rows()
            );
        }else
            return [];
    }

    /**
     * @param int $key
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            $this->db->delete($this->table)->where('promotion_id','=',$key)->run();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $key
     * @param Promotion $data
     * @return void
     */
    public function update($key, $data)
    {
        $this->db->update($this->table)
            ->fields(['product_id', 'discount_percentage', 'start_date', 'end_date'])
            ->value([
                $data->productId(),
                $data->discountPercentage(),
                $data->startDate()->format('Y-m-d'),
                $data->endDate()->format('Y-m-d')
            ])
            ->where('promotion_id', '=', $key)
            ->run();
    }

    /**
     * @return array{draw: int, recordsTotal: int, recordsFiltered: mixed, data: array}
     */
    public function pagination($draw, $search, $order, $length, $start): array
    {
        $recordsTotal = $this->db->find($this->table)->select('count(*) as `count`')
            ->innerJoin('Product', [['field'=>"{$this->table}.product_id",'condition'=>'=','value'=>'Product.product_id']])
            ->row()['count'];
        $data = [];

        $query = $this->db->find($this->table)->select('*')
            ->innerJoin('Product', [['field'=>"{$this->table}.product_id",'condition'=>'=','value'=>'Product.product_id']]);

        $columns = array(
            0 => '`name`',
            1 => 'discount_percentage',
            2 => 'start_date',
            3 => 'end_date',
            4 => 'price',
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            if (!empty($searchValue)) $query->where('`name`', 'like', "$searchValue%", 'OR');
            if (is_numeric($searchValue)) $query->where('discount_percentage', '=', $searchValue, 'OR');
        }

        if(isset($order))
        {
            $column_name = $order[0]['column'];
            $dir = $order[0]['dir'];
            $query->orderBy("{$columns[$column_name]} $dir");
        }
        else
        {
            $query->orderBy("{$columns[0]} desc");
        }

        $recordsFiltered = count($query->rows());

        if($length != -1)
        {
            $query->limit($length)->offset($start);
        }

        $rows = $query->rows();

        foreach ($rows as $row){
            $data[] = [
                $row['name'],
                $row['discount_percentage'],
                $row['start_date'],
                $row['end_date'],
                $row['price'],
                $row['price'] - $row['price'] * intval($row['discount_percentage'])/100,
                $row['promotion_id'],
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }

    protected function rowToClass($row): Promotion
    {
        return new Promotion(
            $row['promotion_id'],
            $row['product_id'],
            $row['discount_percentage'],
            new DateTime($row['start_date']),
            new DateTime($row['end_date'])
        );
    }
}