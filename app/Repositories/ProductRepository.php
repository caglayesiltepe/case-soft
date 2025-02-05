<?php

namespace App\Repositories;

use App\Interfaces\CreateInterface;
use App\Interfaces\DeleteInterface;
use App\Interfaces\FindInterface;
use App\Interfaces\GetAllInterface;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements CreateInterface, DeleteInterface, FindInterface, GetAllInterface
{
    /**
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $page, int $perPage): LengthAwarePaginator
    {
        return Product::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = Product::find($id);

        return $product->delete();
    }
}
