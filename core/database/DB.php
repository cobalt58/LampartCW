<?php
namespace core\database;

use mysqli;
use mysqli_sql_exception;

class DB
{
    /**
     * @param $config
     * array ['host'=>'', 'port'=>'', 'user'=>'', 'password'=>'', 'dbname'=>'']
     */
    private array $config;
    private mysqli $connection;

    public function __construct($config)
    {
        $this->config = $config;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->connection = new mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                $config['dbname'],
                $config['port'],
            );
        }catch (mysqli_sql_exception $ex){
            abort(505, 'Something went wrong! We are already working on it. Please try later.');
        }
    }

    public function getWhere(QueryBuilder $q, array $rule, string $searchMode)
    {
        $SEARCH_OPERATIONS = [
            'eq'=>'=',
            'ne'=>'<>',
            'lt'=>'<',
            'le'=>'<=',
            'gt'=>'>',
            'ge'=>'>='
        ];

        if (array_key_exists($rule['op'], $SEARCH_OPERATIONS)){
            $q->where("`{$rule['field']}`", $SEARCH_OPERATIONS[$rule['op']], $this->connection->real_escape_string($rule['data']), $searchMode);
            return;
        }

        switch ($rule['op']){
            case 'bw':
                $q->where($rule['field'], 'like', "{$rule['data']}%",$searchMode);
                break;
            case 'nw':
                $q->where($rule['field'], 'not like', "{$rule['data']}%",$searchMode);
                break;
            case 'ew':
                $q->where($rule['field'], 'like', "%{$rule['data']}",$searchMode);
                break;
            case 'en':
                $q->where($rule['field'], 'not like', "%{$rule['data']}",$searchMode);
                break;
            case 'nc':
                $q->where($rule['field'], 'not like', "%{$rule['data']}%",$searchMode);
                break;
            default:
                $q->where($rule['field'], 'like', "%{$rule['data']}%",$searchMode);
                break;
        }

    }

    public function __destruct()
    {
        $this->connection->close();
    }

    public function query($sql){
        $result = $this->connection->query($sql);
        if (is_bool($result)) return $result;
        $rows = [];
        while($row = $result->fetch_assoc()){
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public function queryOne($sql){
        $result = $this->connection->query($sql);
        if (is_bool($result)) return $result;
        $row = $result->fetch_assoc();
        $result->free_result();
        return $row;
    }

    public function realEscapeString($str): string
    {
        return $this->connection->real_escape_string($str);
    }

    public function find($from): QueryBuilder
    {
        return new QueryBuilder($this, $from);
    }

    public function insert($to): QueryBuilder
    {
        return new QueryBuilder($this, $to, 'insert');
    }

    public function update(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table, 'update');
    }

    public function delete(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table, 'delete');
    }
}