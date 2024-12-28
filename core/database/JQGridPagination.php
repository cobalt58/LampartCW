<?php
namespace core\database;

use core\database\DB;
use core\Request;

class JQGridPagination
{
    private string $table;
    private DB $db;
    public function __construct($db, $table)
    {
        $this->table = $table;
        $this->db = $db;
    }

    public function table(): string
    {
        return $this->table;
    }

    public function paginate($page, $rows, $sidx, $sord): array
    {
        // Отримання параметрів пагінації
        $page = isset($page) ? intval($page) : 1; // Поточна сторінка
        $rowsPerPage = isset($rows) ? intval($rows) : 10; // Кількість рядків на сторінці

        // Розрахунок початкового та кінцевого рядка
        $startRow = ($page - 1) * $rowsPerPage;
        $endRow = $startRow + $rowsPerPage;

        // Запит для отримання даних з пагінацією
        $q = $this->db->find($this->table)
            ->select('*')
            ->orderBy("`{$sidx}` {$sord}")

            ->limit($startRow)
            ->offset($endRow);

        if (Request::post('_search')){
            $searchOptions = json_decode(Request::post('filters'), true);
            $searchMode = $searchOptions['groupOp'];
            foreach ($searchOptions['rules'] as $rule) {
                $this->db->getWhere($q, $rule, $searchMode);
            }
        }

        $result = $q->rows();
        $data = array_values($result);

// Запит для отримання загальної кількості записів
        $totalRecordsSql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $totalRecords = intval($this->db->queryOne($totalRecordsSql)['total']);
        $totalRecords = count($data);

// Створення JSON-відповіді
        return array(
            "rows" => $data,
            "page" => $page,
            "total" => ceil($totalRecords/$rows),
            "records" => $totalRecords
        );
    }
}