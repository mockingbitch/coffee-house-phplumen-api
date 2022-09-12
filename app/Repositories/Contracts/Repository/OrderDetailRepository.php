<?php

namespace App\Repositories\Contracts\Repository;

use App\Models\OrderDetail;
use App\Repositories\Contracts\Interface\OrderDetailRepositoryInterface;
use App\Repositories\BaseRepository;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function getModel()
    {
        return OrderDetail::class;
    }
}