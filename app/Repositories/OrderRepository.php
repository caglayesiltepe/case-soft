<?php

namespace App\Repositories;

use App\Interfaces\CreateInterface;
use App\Interfaces\DeleteInterface;
use App\Interfaces\FindInterface;
use App\Interfaces\GetAllInterface;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements CreateInterface, DeleteInterface, GetAllInterface, FindInterface
{
    /**
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }


    /**
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $page, int $perPage): LengthAwarePaginator
    {
        return Order::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $order = Order::find($id);

        return $order->delete();
    }
}
