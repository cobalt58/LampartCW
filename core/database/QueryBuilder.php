<?php
namespace core\database;

class QueryBuilder {
    private $db;
    protected $table = '';
    protected $select = [];
    protected $values = [];
    protected $where = [];
    protected $order = [];
    protected $join = [];
    protected $group = [];
    protected $offset = null;
    protected $limit = null;

    protected $type;

    public function __construct($db, $from, $type = 'select') {
        $this->table = $from;
        $this->db = $db;
        $this->type = $type;
    }

    public function offset($o): QueryBuilder
    { $this->offset = $o; return $this; }
    public function limit($l): QueryBuilder
    { $this->limit = $l; return $this; }

    public function leftJoin($t, $c): QueryBuilder
    {
        $this->join[] = [
            'type' => ' LEFT JOIN ',
            'table' => $t,
            'condition' => $c
        ];
        return $this;
    }

    public function innerJoin($t, $c): QueryBuilder
    {
        $this->join[] = [
            'type' => ' INNER JOIN ',
            'table' => $t,
            'condition' => $c
        ];
        return $this;
    }

    public function select($fields): QueryBuilder
    {
        if (is_array($fields)) {
            foreach ($fields as $v) {
                array_push($this->select, $v);
            }
        } else {
            array_push($this->select, $fields);
        }
        return $this;
    }

    public function orderBy($fields): QueryBuilder
    {
        if (is_array($fields)) {
            foreach ($fields as $v) {
                array_push($this->order, $v);
            }
        } else {
            array_push($this->order, $fields);
        }
        return $this;
    }

    public function groupBy($fields): QueryBuilder
    {
        if (is_array($fields)) {
            foreach ($fields as $v) {
                array_push($this->group, $v);
            }
        } else {
            array_push($this->group, $fields);
        }
        return $this;
    }

    public function where($field, $condition, $value, $type = 'AND'): QueryBuilder
    {
        $this->where[] = [
            'type' => $type,
            'field' => $field,
            'condition' => $condition,
            'value' => $value
        ];
        return $this;
    }

    private function buildWhere($where, $isJoin = false): string
    {
        $wh = '';
        foreach($where as $item) {
            if ($wh) $wh .= ' ' . $item['type'];
            if ($item['value'] == 'null' || $item['value'] == null || $isJoin || mb_strpos($item['value'],'(')!==false ||mb_strpos($item['value'],'and')!==false)
                $wh .=  ' ' . $item['field'] . ' '. $item['condition'].' '. $item['value'];
            else
                $wh .=  ' ' . $item['field'] . ' '. $item['condition']. " '". $item['value'] . "'";
        }

        return $wh;
    }

    public function fields($fields): QueryBuilder
    {
        foreach ($fields as $field) {
            $this->select[] = $field;
        }
        return $this;
    }

    public function value($values): QueryBuilder
    {
        foreach ($values as $value) {
            $this->values[] = $value;
        }
        return $this;
    }

    public function sql(): string
    {
        $sql = '';
        if ($this->type == 'select') {
            $sql = 'SELECT ';
            if (!$this->select) {
                $sql .= ' * ';
            } else {
                $sql .= implode(',', $this->select);
            }
            $sql .= ' FROM ' . $this->table;
            foreach ($this->join as $item) {
                $sql .= ' ' . $item['type'] . ' ' . $item['table'] . ' ON ' . $this->buildWhere($item['condition'], true);
            }
            if ($this->where) {
                $sql .= ' WHERE ' . $this->buildWhere($this->where);
            }
            if ($this->group) $sql .= ' GROUP BY ' . implode(',', $this->order);
            if ($this->order) $sql .= ' ORDER BY ' . implode(',', $this->order);
            if ($this->offset && $this->limit) $sql .= ' LIMIT ' . $this->offset . ',' . $this->limit;
            else if ($this->limit) $sql .= ' LIMIT ' . $this->limit;
        }
        elseif ($this->type == 'insert'){
            $sql = 'INSERT INTO ' . $this->table;
            $sql .= ' (' . implode(', ', $this->select) . ') ';
            if ($this->values){
                $sql.= ' VALUE(';
                foreach ($this->values as $value){
                    if ($value == 'null')
                        $sql.=$value.',';
                    else
                        $sql.= "'".$value."',";
                }
                $sql = mb_substr($sql, 0, -1);
                $sql.= ')';
            }
        }elseif ($this->type == 'update'){
            $sql = 'UPDATE '.$this->table.' SET';
            for ($i = 0; $i < count($this->select); $i++) {
                if ($this->values[$i] == 'null')
                    $sql.=" ".$this->select[$i]. " = ". $this->values[$i]. ", ";
                else
                    $sql.=" ".$this->select[$i]. " = '". $this->values[$i]. "', ";
            }
            $sql = mb_substr($sql, 0, -2);
            if ($this->where) {
                $sql .= ' WHERE ' . $this->buildWhere($this->where);
            }
        }elseif ($this->type == 'delete'){
            $sql = 'DELETE FROM '.$this->table;
            $sql .= ' WHERE ' .$this->buildWhere($this->where);
        }
        return $sql;
    }



    public function rows(): array
    {
        return $this->db->query($this->sql());
    }

    public function row() {
        return $this->db->queryOne($this->sql());
    }

    public function run(){
        $this->db->queryOne($this->sql());
    }

}