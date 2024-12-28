<?php
namespace models\orm;

use core\database\DB;
use core\database\DBModel;
use core\database\JQGridPagination;
use Exception;

class Roles extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, 'user_roles');
    }

    /**
     * @param Role $data
     * @return void
     */
    public function add($data)
    {
        $this->db->insert($this->table)
            ->fields(['role_name','role_acl'])
            ->value([$data->title(),$data->acl()])
            ->run();
    }

    /**
     * @return Role|Role[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_array($key) && is_int($key[0])){
            $rows = $this->db->find($this->table)->select('*')->where('id_role','in', '('. implode(',',$key) .')')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('id_role','=',$key)->row());
        }

        return $this->rowsToClass($rows);
    }

    public function find($field, $condition, $value, $limit = null): array
    {
        $rows = $this->db->find($this->table)
            ->select('*')
            ->where($field, $condition, $value);
        if (!is_null($limit)) $rows->limit($limit);
        return $this->rowsToClass($rows->rows());
    }

    public function findOne($field, $condition, $value): ?Role
    {
        $rows = $this->find($field, $condition, $value, 1);
        return $rows[0];
    }

    public function findWithParams(array $params, $limit = null): array
    {
        $rows = $this->db->find($this->table)->select('*');

        foreach ($params as $param){
            $rows->where($param[0], $param[1], $param[2], $param[3] ?? 'and');
        }

        if (!is_null($limit)) $rows->limit($limit);

        $rows = $rows->rows();
        return $this->rowsToClass($rows);
    }

    /**
     * @param int $key
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            $role_id = $this->db->find('user_roles')
                ->select('id_role')
                ->where('role_acl', '<', '100')
                ->where('role_acl', '>', '0')
                ->where('id_role', '<>', $key)
                ->orderBy('role_acl asc')
                ->limit(1)
                ->row()['id_role'];

            $this->db->update('users')
                ->fields(['id_role'])
                ->value([$role_id])
                ->where('id_role', '=', $key)
                ->run();

            $this->db->delete($this->table)->where('id_role','=',$key)->run();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $key
     * @param Role $data
     * @return void
     */
    public function update($key, $data)
    {
        $this->db->update($this->table)
            ->fields(['role_name', 'role_acl'])
            ->value([$data->title(), $data->acl()])
            ->where('id_role', '=', $data->id())
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
            0 => 'role_name',
            1 => 'role_acl',
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            if (!empty($searchValue)) $query->where('role_acl', '=', "$searchValue", 'OR');
            $query->where('role_name', 'like', "%$searchValue%", 'OR');
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
                $row['role_name'],
                $row['role_acl'],
                $row['id_role'],
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }

    protected function rowToClass($row): Role
    {
        return new Role(
                $row['id_role'],
                $row['role_name'],
                $row['role_acl']
            );
    }

}