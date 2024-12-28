<?php
namespace models\orm;

use core\database\DB;
use core\database\DBModel;
use models\Category;
use models\ProductCategory;
use models\ProductProperty;

class Categories extends DBModel {
    public function __construct(DB $db)
    {
        parent::__construct($db, 'Category');
    }

    /**
     * @param Category $data
     * @return void
     */
    public function add($data)
    {
        $this->db->insert($this->table)
            ->fields(['name', 'parent_category_id'])
            ->value([$data->title(), $data->parent() ?? 'null'])
            ->run();
    }


    /**
     * @param $key
     * @return Category[]|false|Category
     */
    public function get($key = null)
    {
        $rows = $this->db->find($this->table)
            ->select('*');

        if (is_null($key)){
            return $this->rowsToClass($rows->rows());
        }

        if (is_numeric($key)){
            $rows->where('category_id', '=', $key);
            return $this->rowToClass($rows->row());
        }

        return false;
    }

    /**
     * @param int $key
     * @param Category $data
     * @return void
     */
    public function update($key, $data)
    {
        $this->db->update($this->table)
            ->fields(['name','parent_category_id'])
            ->value([$data->title(), $data->parent() ?? 'null'])
            ->where('category_id', '=', $key)
            ->run();
    }

    public function delete($key): bool
    {
        try {
            $parentCategory = $this->get($key)->parent();

            $this->db->update('places')
                ->fields(['category_id'])
                ->value([$parentCategory ?? -1])
                ->where('category_id', '=', $key)
                ->run();

            $this->db->update($this->table)
                ->fields(['parent_category_id'])
                ->value([$parentCategory ?? 'null'])
                ->where('parent_category_id', '=', $key)
                ->run();

            $this->db->delete($this->table)
                ->where('category_id', '=', $key)
                ->run();
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function tree($search = null): array
    {
        /** @var Category[] $allCategories */
        $allCategories = $this->get();

        $allCategories = array_map(function ($value){
            return [
                'category'=>$value,
                'children'=>[]
            ];
        }, $allCategories);

        $rootCategories = array_filter($allCategories, function ($value){
            return !$value['category']->parent();
        });

        $this->formatTree($rootCategories, $allCategories);

        if (!is_null($search)){
            $searched = [];
            $this->searchTree($searched, $rootCategories, $search);
            return array_values($searched);
        }

        return array_values($rootCategories);
    }

    private function searchTree(&$searched, &$all, &$search)
    {
        foreach ($all as $item){
            if (mb_stripos($item['category']->title(), $search) !== false){
                $searched[] = $item;
                continue;
            }

            if (!empty($item['children'])){
                self::searchTree($searched, $item['children'], $search);
            }
        }
    }

    private function formatTree(&$categories, &$allCategories)
    {
        foreach ($categories as &$category) {
            $category['children'] = array_values(array_filter($allCategories, function ($value) use ($category) {
                return $value['category']->parent() == $category['category']->id();
            }));

            if (!empty($category['children'])){
                self::formatTree($category['children'], $allCategories);
            }

        }
    }

    public function getAllChild(int $id = null): array{
        $base_cats = [];
        $rows = $this->db->query('select * from Category where parent_category_id ' . (($id == null)?'is null':'= '.$id));

        foreach ($rows as $row) {
            $base_cats[$row['name']] = [intval($row['category_id'])];

            //$base_cats[$row['name']] = [['name'=>$row['name'], 'id'=> intval($row['id'])]];
            $stack = [$row['category_id']];

            while (!empty($stack)){
                $id = array_pop($stack);
                $rows2 = $this->db->query('select * from Category where parent_category_id = '. $id);
                foreach ($rows2 as $item) {
                    $base_cats[$row['name']][] = intval($item['category_id']);
                    //$base_cats[$row['name']][] = [['name'=>$item['name'], 'id'=> intval($item['id'])]];
                    $stack[] = intval($item['category_id']);
                }
            }
        }

        return $base_cats;
    }

    protected function rowToClass($row): Category
    {
        return new Category(
            $row['category_id'],
            $row['name'],
            $row['parent_category_id']
        );
    }

    /**
     * @param $key
     * @return array[ProductCategory]
     */
    public function getProductCategories($key): array
    {
        if (is_null($key)){
            return [];
        }
        if (is_numeric($key)){
            $ar = [];

            $rows =
                $this->db->find('Product_Category')
                    ->select('*')
                    ->innerJoin('Category', [['field'=>'Category.category_id','condition'=>'=','value'=>'Product_Category.category_id']])
                    ->where('product_id','=',$key)
                    ->rows();

            foreach ($rows as $row){

                $ar[] = new ProductCategory(
                    $row['product_id'],
                    $row['category_id'],
                    $row['name'],
                );
            }

            return $ar;
        }else
            return [];
    }
}