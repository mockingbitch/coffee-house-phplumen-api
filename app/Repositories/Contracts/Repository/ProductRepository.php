<?php

namespace App\Repositories\Contracts\Repository;

use App\Models\Product;
use App\Repositories\Contracts\Interface\ProductRepositoryInterface;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    /**
     * @param integer $category_id
     * 
     * @return object
     */
    public function getProductsByCategory(int $category_id) : object
    {
        return $this->model->where('category_id', $category_id)->get();
    }
}