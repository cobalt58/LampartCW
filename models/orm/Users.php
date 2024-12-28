<?php

namespace models\orm;

use core\database\DB;
use core\database\DBModel;
use DateTime;
use models\User;

class Users extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'User');
        $this->baseQuery = $this->db->find($this->table)->select('*');
    }

    /**
     * @param User $data
     */
    public function add(User $data)
    {
        $fieldsToAdd = ['name', 'middlename', 'lastname', 'email', 'password', 'phone', 'role'];
        $values = [$data->name(), $data->patronymic(), $data->surname(),  $data->email(), $data->hash(), $data->phone(), $data->role()];

        $this->db->insert($this->table)
            ->fields($fieldsToAdd)
            ->value($values)
            ->row();
        return $this->db->find($this->table)->select('user_id')->orderBy('user_id DESC')->limit(1)->row()['user_id'];
    }

    /**
     * @param mixed $key user id
     * @return false|User|User[]
     */
    public function get($key = null)
    {
        $queryBuilder = $this->db->find($this->table)->select('*');
        if (is_null($key))
            return $this->rowsToClass(
                $queryBuilder->rows()
            );

        if (is_numeric($key))
            return $this->rowToClass(
                $queryBuilder->where("{$this->table}.user_id", '=', $key)->row()
            );

        return false;
    }

    public function pagination($draw, $search, $order, $length, $start)
    {
        $recordsTotal = $this->db->find($this->table)->select('count(*) as `count`')->row()['count'];
        $data = [];

        $query = $this->db->find($this->table)->select('*');

        $columns = array(
            0 => 'email',
            1 => 'lastname',
            2 => 'name',
            3 => 'middlename',
            4 => 'phone',
            5 => 'role',
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            $query->where('email', 'like', "$searchValue%", 'OR');
            $query->where('name', 'like', "$searchValue%", 'OR');
            $query->where('lastname', 'like', "$searchValue%", 'OR');
            $query->where('middlename', 'like', "$searchValue%", 'OR');
            $query->where('phone', 'like', "$searchValue%", 'OR');
            $query->where('role', 'like', "$searchValue%", 'OR');
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
                $row['email'],
                $row['name'],
                $row['lastname'],
                $row['middlename'],
                $row['phone'],
                $row['role'],
                $row['user_id'],
                ($row['role'] == 'admin' || $row['role'] == 'manager' ? 100 : ($row['role'] == 'ban' ? -1 : 10))
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }

    public function delete($key): bool
    {
        /*
        $this->db->delete('reviews')->where('user_id', '=', $key)->run();
        $this->db->delete('likes')->where('user_id', '=', $key)->run();
        $this->db->delete('requests')->where('user_id', '=', $key)->run();
        */
        $this->db->delete($this->table)->where('user_id', '=', $key)->run();

        return true;
    }

    /**
     * @param int $key
     * @param User $data
     * @return void
     */
    public function update($key, $data)
    {

        $fieldsToUpdate = ['name', 'middlename', 'lastname', 'email', 'password', 'phone', 'role'];
        $values = [$data->name(), $data->patronymic(), $data->surname(),  $data->email(), $data->hash(), $data->phone(), $data->role()];

        $this->db->update($this->table)
            ->fields($fieldsToUpdate)
            ->value($values)
            ->where('user_id', '=', $key)
            ->row();
    }


    public function ban($key)
    {
        $this->db->update($this->table)
            ->fields(['role'])
            ->value(['ban'])
            ->where('user_id', '=', $key)
            ->run();
    }

    public function unban($key)
    {
        $this->db->update($this->table)
            ->fields(['role'])
            ->value(['user'])
            ->where('user_id', '=', $key)
            ->run();
    }

    protected function rowToClass($row): User
    {

        return new User(
            $row['user_id'],
            $row['lastname'],
            $row['name'],
            $row['middlename'],
            $row['email'],
            $row['phone'],
            $row['password'],
            $row['role'],
            $row['total_spent_amount']
        );
    }
}