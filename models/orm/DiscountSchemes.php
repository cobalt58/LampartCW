<?php

namespace models\orm;

use core\App;
use core\database\DB;
use core\database\DBModel;
use Exception;
use models\DiscountScheme;
use models\User;

class DiscountSchemes extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Discount_Scheme');
    }

    /**
     * @param DiscountScheme $data
     * @return void
     */
    public function add($data)
    {
        $this->db->insert($this->table)
            ->fields(['min_spent_amount','discount_percentage'])
            ->value([$data->minSpentAmount(), $data->discountPercentage()])
            ->run();
    }

    /**
     * @return DiscountScheme|DiscountScheme[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])){
            $rows = $this->db->find($this->table)->select('*')->where('discount_scheme_id','in', '('. implode(',',$key) .')')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('discount_scheme_id','=',$key)->row());
        }

        return $this->rowsToClass($rows);
    }

    /**
     * @param int $key
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            $this->db->delete($this->table)->where('discount_scheme_id','=',$key)->run();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $key
     * @param DiscountScheme $data
     * @return void
     */
    public function update($key, $data)
    {
        $this->db->update($this->table)
            ->fields(['min_spent_amount','discount_percentage'])
            ->value([$data->minSpentAmount(), $data->discountPercentage()])
            ->where('discount_scheme_id', '=', $key)
            ->run();
    }

    public function getDiscount($user_id)
    {

        /** @var Users $users */
        $users = App::resolve(Users::class);
        /** @var User $user */
        $user = $users->get($user_id);

        $discount = $this->db->find($this->table)
            ->select('discount_percentage')
            ->where('min_spent_amount', '<=', $user->totalSpentAmount())
            ->orderBy('min_spent_amount DESC')
            ->limit(1)
            ->row();
        return $discount ? $discount['discount_percentage'] : 0;
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
            0 => 'min_spent_amount',
            1 => 'discount_percentage'
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            if (is_numeric($searchValue))$query->where('min_spent_amount', '=', $searchValue, 'OR');
            if (is_numeric($searchValue))$query->where('discount_percentage', '=', $searchValue, 'OR');
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
                $row['min_spent_amount'],
                $row['discount_percentage'],
                $row['discount_scheme_id'],
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }

    protected function rowToClass($row): DiscountScheme
    {
        return new DiscountScheme(
            $row['discount_scheme_id'],
            $row['min_spent_amount'],
            $row['discount_percentage']
        );
    }
}