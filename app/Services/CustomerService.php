<?php

namespace App\Services;

use App\Http\Requests\CustomerCreateRequest;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

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
     * @return array
     */
    public function getAllCustomers(): array
    {
        $customers = $this->customerRepository->getCustomers();
        return $this->getCustomerMap($customers);
    }

    /**
     * @param $customers
     * @return array
     */
    private function getCustomerMap($customers): array
    {
        return $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'since' => $customer->since,
                'revenue' => number_format($customer->revenue, 2),
            ];
        })->toArray();
    }

    /**
     * @param array $request
     * @return Customer
     * @throws Exception
     */
    public function handleCreateCustomer(array $request): Customer
    {
        DB::beginTransaction();
        try {
            $customer = $this->customerRepository->create([
                'name' => $request['name'],
                'since' => $request['since'],
                'revenue' => $request['revenue'],
            ]);

            DB::commit();

            return $customer;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer creation failed', ['error' => $e->getMessage()]);
            throw new Exception("Müşteri oluşturulamadı." . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function handleDeleteCustomer(int $id): bool
    {
        $customerDelete = $this->customerRepository->delete($id);

        if (!$customerDelete) {
            Log::error('Customer delete failed', ['error' => 'Customer not found', 'customer_id' => $id]);
            return false;
        }

        return true;
    }

}
