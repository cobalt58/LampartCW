<?php

namespace models\orm;

use core\App;
use core\database\DB;
use core\database\DBModel;
use core\exceptions\ServerException;
use core\Uploader;
use Exception;
use models\Category;
use models\Product;

class Products extends DBModel
{
    public function __construct(DB $db)
    {
        parent::__construct($db, "Product");
    }

    /**
     * @param Product $data
     * @return void
     */
    public function add($data, $files = null)
    {

        $this->db->insert($this->table)
            ->fields(['name', 'description', 'price', 'quantity'])
            ->value([$data->name(), $data->description(), $data->price(), $data->quantity()])
            ->run();

        $id = $this->db->find($this->table)
            ->select('product_id')
            ->orderBy('product_id DESC')
            ->row()['product_id'];

        foreach ($data->properties() as $key=>$value) {
            $this->db->insert('Product_Property_Value')
                ->fields(['product_id','property_id','value'])
                ->value([$id, $key, $value])
                ->run();
        }
        foreach ($data->categories() as $category) {
            $this->db->insert('Product_Category')
                ->fields(['product_id', 'category_id'])
                ->value([$id, $category])
                ->run();
        }

        if ($files['media']) {
            $targetDirectory = base_path("products-images/product-{$id}");

            $upload = Uploader::upload($files, 'media', $targetDirectory, false);

            if ($upload === false) {
                throw ServerException::throw('Unable to create folder for images');
            }
        }
    }

    /**
     * @return Product|Product[]
     */
    public function get($key = null)
    {
        $rows = [];
        if (is_null($key)){
            $rows = $this->db->find($this->table)->select('*')->rows();
        }
        if (is_numeric($key)){
            return $this->rowToClass($this->db->find($this->table)->select('*')->where('product_id','=',$key)->row());
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

            $this->db->delete('Product_Property_Value')->where('product_id', '=', $key)->run();
            $this->db->delete('Product_Category')->where('product_id', '=', $key)->run();
            $this->db->delete('Promotion')->where('product_id', '=', $key)->run();
            $this->db->delete($this->table)->where('product_id','=',$key)->run();
            $sep = DIRECTORY_SEPARATOR;
            deleteDirectory(base_path("products-images{$sep}product-{$key}"));
            return true;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @param int $id
     * @param Product $data
     * @return void
     */
    public function update($id, $data, $files = null): void
    {
        $this->db->update($this->table)
            ->fields(['name', 'description', 'price', 'quantity'])
            ->value([$data->name(), $data->description(), $data->price(), $data->quantity()])
            ->where('product_id', '=', $data->id())
            ->run();

        $this->db->delete('Product_Property_Value')->where('product_id','=',$id)->run();
        $this->db->delete('Product_Category')->where('product_id','=',$id)->run();

        foreach ($data->properties() as $key=>$value) {
            $this->db->insert('Product_Property_Value')
                ->fields(['product_id','property_id','value'])
                ->value([$id, $key, $value])
                ->run();
        }
        foreach ($data->categories() as $category) {
            $this->db->insert('Product_Category')
                ->fields(['product_id', 'category_id'])
                ->value([$id, $category])
                ->run();
        }

        if ($files['media']) {
            $targetDirectory = base_path("products-images/product-{$id}");

            $upload = Uploader::upload($files, 'media', $targetDirectory, false);

            if ($upload === false) {
                throw ServerException::throw('Unable to create folder for images');
            }
        }
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
            0 => 'name',
            1 => 'description',
            2 => 'price',
            3 => 'quantity',
        );

        if(isset($search['value']))
        {
            $searchValue = $search['value'];
            $query->where('name', 'like', "%$searchValue%", 'OR');
            $query->where('description', 'like', "$searchValue%", 'OR');
            if (is_numeric($searchValue)) $query->where('price', '=', $searchValue, 'OR');
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

        /** @var Categories $categories */
        $categories = App::resolve(Categories::class);
        foreach ($rows as $row){
            $data[] = [
                $row['name'],
                $categories->getProductCategories($row['product_id'])[0]->name(),
                $row['price'],
                $row['quantity'],
                $row['product_id'],
            ];
        }
        return [
            'draw'=>intval($draw),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data
        ];
    }
    public function countAll()
    {
        return intval($this->db->find($this->table)->select('count(*) as `count`')->row()['count']);
    }

    public function categoriesChildIds($id)
    {
        /* @var Categories $categories */
        $categories = App::resolve(Categories::class);
        $base_cats = [intval($id)];
        /* @var Category[] $rows */
        $rows = $categories->find('parent_category_id', (($id == null) ? 'is null' : '='), $id);

        foreach ($rows as $row) {
            $base_cats[] = intval($row->id());
            $stack = [$row->id()];

            while (!empty($stack)) {
                $id = array_pop($stack);
                $nrows = $categories->find('parent_category_id', '=', $id);
                foreach ($nrows as $nrow) {
                    $base_cats[] = intval($nrow->id());
                    $stack[] = intval($nrow->id());
                }
            }
        }

        return $base_cats;
    }

    public function neoPagination($draw, $search, $order, $length, $start)
    {
        $recordsFiltered = $this->countAll();
        $data = [];

        $query = $this->db->find('productwithdiscount')
            ->select('*');

        $columns = array(
            0 => 'name',
            1 => 'original_price',
            2 => 'description',
        );

        if (isset($search)) {
            $mode = $search['mode'] =='and';//'all' ? 'AND' : 'OR';
            if (!empty($search['value'])) {
                $query->where('name', 'like', "%{$search['value']}%");
            }

            if (!empty($search['price_from'])) {
                $query->where('original_price', '>=', "{$search['price_from']}");
            }

            if (!empty($search['price_to'])) {
                $query->where('original_price', '<=', "{$search['price_to']}");
            }

            if (isset($search['category']) && (is_numeric($search['category']) || is_string($search['category']))) {
                $categories = $this->categoriesChildIds($search['category']);
                $query->where('EXISTS (
                                    SELECT 1
                                    FROM Product_Category pc
                                    WHERE pc.product_id = productwithdiscount.product_id
                                      AND pc.category_id', 'in', '(' . implode(',', $categories) . '))');
            }
        }



        if (isset($order)) {
            $column_name = $order[0]['column'];
            $dir = $order[0]['dir'];
            $query->orderBy("{$columns[$column_name]} $dir");
        } else {
            $query->orderBy("{$columns[0]} desc");
        }

        $recordsTotal = count($query->rows());

        if ($length != -1) {
            $query->limit($length)->offset($start);
        }

        $rows = $query->rows();

        foreach ($rows as $row) {
            $data[] = [
                'product' => $this->rowToClass($row),
                'original_price' => $row['original_price'],
                'discounted_price' => $row['discounted_price'],
            ];
        }
        return [
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    protected function rowToClass($row): Product
    {
        $this->db->find('Product_Property_Value')->where('product_id', '=', $row['product_id'])->rows();

        /** @var Properties $properties */
        $properties = App::resolve(Properties::class);
        /** @var Promotions $promotions */
        $promotions = App::resolve(Promotions::class);
        /** @var Categories $categories */
        $categories = App::resolve(Categories::class);
        return new Product(
            $row['product_id'],
            $row['name'],
            $row['description'],
            $row['price'] ?? $row['original_price'],
            $row['quantity'],
            $promotions->getProductPromotions($row['product_id']),
            $categories->getProductCategories($row['product_id']),
            $properties->getProductProperties($row['product_id'])
        );
    }

}