<?php
namespace core\database;

class DBModel
{
    protected DB $db;
    protected string $table;
    protected ?QueryBuilder $baseQuery;

    /**
     * @param DB $db
     * @param string $table
     */
    protected function __construct(DB $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
        $this->baseQuery = null;
    }

    protected function rowToClass($row)
    {

    }

    protected function rowsToClass(array $rows): array
    {
        return array_values(array_map(function ($value){
            return $this->rowToClass($value);
        }, $rows));
    }

    public function find($field, $condition, $value, $limit = null): array
    {
        $q = $this->baseQuery ?? $this->db->find($this->table)->select('*');

        $q = $q->where($field, $condition, $value);

        if (!is_null($limit)) $q->limit($limit);
        return $this->rowsToClass($q->rows());
    }

    public function findInTable($table, $field, $condition, $value, $limit = null): array
    {
        $rows = $this->db->find($table)->select('*');

        $rows = $rows->where($field, $condition, $value);

        if (!is_null($limit)) $rows->limit($limit);
        return $this->rowsToClass($rows->rows());
    }

    public function findOne($field, $condition, $value)
    {
        return $this->find($field, $condition, $value, 1)[0];
    }

    public function findWithParams(array $params, $limit = null): array
    {
        //TODO
        $rows = $this->baseQuery ?? $this->db->find($this->table)->select('*');

        foreach ($params as $param){
            $rows->where($param[0], $param[1], $param[2], $param[3] ?? 'and');
        }

        if (!is_null($limit)) $rows->limit($limit);

        $rows = $rows->rows();
        return $this->rowsToClass($rows);
    }

    public function findInTableWithOParams($table, array $params, $limit = null): array
    {
        $rows = $this->db->find($table)->select('*');

        foreach ($params as $param){
            $rows->where($param[0], $param[1], $param[2], $param[3] ?? 'and');
        }

        if (!is_null($limit)) $rows->limit($limit);

        return $this->rowsToClass($rows->rows());
    }
}