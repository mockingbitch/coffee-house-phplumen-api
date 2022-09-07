<?php

namespace App\Repositories\Contracts\Repository;

use App\Models\Category;
use App\Repositories\Contracts\Interface\CategoryRepositoryInterface;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return Category::class;
    }
}