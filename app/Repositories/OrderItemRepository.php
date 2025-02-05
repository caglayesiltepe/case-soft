<?php

namespace App\Repositories;

use App\Interfaces\CreateInterface;
use App\Models\OrderItem;

class OrderItemRepository implements CreateInterface
{
    /**
     * @param array $data
     * @return OrderItem
     */
    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }
}
