<?php

namespace App\Repositories\Contracts\Repository;

use App\Models\CartDetail;
use App\Repositories\Contracts\Interface\CartDetailRepositoryInterface;
use App\Repositories\BaseRepository;

class CartDetailRepository extends BaseRepository implements CartDetailRepositoryInterface
{
    public function getModel()
    {
        return CartDetail::class;
    }
}