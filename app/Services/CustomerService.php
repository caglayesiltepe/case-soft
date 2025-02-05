<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(
        CustomerRepository $customerRepository
    )
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param int $page ,
     * @param int $perPage
     * @return array
     */
    public function getAllCustomers(int $page, int $perPage): array
    {
        $customers = $this->customerRepository->getAll($page, $perPage);

        return $this->getCustomerMap($customers);
    }


    /**
     * @param $customers
     * @return array
     */
    private function getCustomerMap($customers): array
    {
        return [
            'data' => $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'since' => $customer->since,
                    'revenue' => number_format($customer->revenue, 2),
                ];
            })->toArray(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'last_page' => $customers->lastPage(),
            ]
        ];
    }


    /**
     * @param array $request
     * @return Customer
     */
    public function handleCreateCustomer(array $request): Customer
    {
        return $this->customerRepository->create([
            'name' => $request['name'],
            'since' => $request['since'],
            'revenue' => $request['revenue'],
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function handleDeleteCustomer(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }

}
