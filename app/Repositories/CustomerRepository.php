<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
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
     * @return Collection
     */
    public function getCustomers():Collection
    {
        return Customer::all();
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

        if (!$customer) {
            return false;
        }

        $customer->delete();

        return true;
    }
}
