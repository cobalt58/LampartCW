<?php

namespace models\orm;

use core\database\DB;
use core\database\DBModel;
use core\database\JQGridPagination;
use Exception;
use models\ProductProperty;
use models\Property;

class Properties extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Property');
    }

    /**
     * @param Property $data
     * @return void
     */
    public function add($data)
    {
        $this->db->insert($this->table)
            ->fields(['name'])
            ->value([$data->name()])
            ->run();
    }

    /**
     * @return Property|Property[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])){
            $rows = $this->db->find($this->table)->select('*')->where('property_id','in', '('. implode(',',$key) .')')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('property_id','=',$key)->row());
        }

        return $this->rowsToClass($rows);
    }

    /**
     * @param $key
     * @return array[ProductProperty]
     */
    public function getProductProperties($key = null): array
    {
        $rows = [];
        if (is_null($key)){
            return [];
        }
        if (is_numeric($key)){
            $ar = [];

            $rows =
                $this->db->find('Product_Property_Value')
                    ->select('*')
                    ->where('product_id','=',$key)
                    ->rows();

            foreach ($rows as $row){
                $ar[] = new ProductProperty(
                    $row['product_id'],
                    $row['property_id'],
                    $row['value'],
                );
            }

            return $ar;
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
            $this->db->delete('Product_Property_Value')->where('property_id', '=', $key)->run();
            $this->db->delete($this->table)->where('property_id','=',$key)->run();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $key
     * @param Property $data
     * @return void
     */
    public function update($key, $data)
    {
        $this->db->update($this->table)
            ->fields(['name'])
            ->value([$data->name()])
            ->where('property_id', '=', $data->id())
            ->run();
    }

    public function jqGridPagination($page, $rows, $sidx, $sord): array
    {
        $pagination = new JQGridPagination($this->db, $this->table);
        return $pagination->paginate($page, $rows, $sidx, $sord);
    }

    /**
     * @return array{draw: int, recordsTotal: int, recordsFiltered: mixed, data: array}
     */
    public function pagination($draw, $search, $order, $length, $start): array
    {
        $recordsTotal = $this->db->find($this->table)->select('count(*) as `count`')->row()['count'];
        $data = [];

        $query = $this->db->find($this->table)->select('*');

        $columns = array(
            0 => 'name'
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            $query->where('name', 'like', "$searchValue%", 'OR');
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
                $row['property_id'],
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }

    protected function rowToClass($row): Property
    {
        return new Property(
            $row['property_id'],
            $row['name']
        );
    }
}