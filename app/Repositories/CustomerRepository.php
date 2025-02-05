<?php

namespace App\Repositories;

use App\Interfaces\CreateInterface;
use App\Interfaces\DeleteInterface;
use App\Interfaces\FindInterface;
use App\Interfaces\GetAllInterface;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CreateInterface, DeleteInterface,FindInterface,GetAllInterface
{
    /**
     * @param int $id
     * @return Customer|null
     */
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $page, int $perPage): LengthAwarePaginator
    {
        return Customer::paginate($perPage, ['*'], 'page', $page);
    }


    /**
     * @param array $data
     * @return Customer
     */
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $customer = Customer::find($id);

        return $customer->delete();
    }
}
