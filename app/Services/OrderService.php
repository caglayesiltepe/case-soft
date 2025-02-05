<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    private DiscountService $discountService;

    public function __construct(
        OrderRepository     $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository   $productRepository,
        CustomerRepository  $customerRepository,
        DiscountService     $discountService
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->discountService = $discountService;
    }

    /**
     * @param array $request
     * @return Order
     * @throws Exception
     */
    public function handleCreateOrder(array $request): Order
    {
        DB::beginTransaction();

        try {
            $customerId = $request['customer_id'];
            $items = $request['items'];
            $customer = $this->validateCustomer($customerId);
            $this->validateStock($items);

            $order = $this->createNewOrder([
                'customer_id' => $customerId,
                'total' => 0
            ]);

            $this->createOrderItemsAndUpdateStock($order, $items);
            $this->applyDiscountAndUpdateRevenue($order, $customer);

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'customer_id' => $customerId,
                'items' => $items
            ]);
            throw new Exception("Sipariş oluşturulamadı" . $e->getMessage());
        }
    }

    /**
     * @param int $customerId
     * @return Customer
     * @throws Exception
     */
    private function validateCustomer(int $customerId): Customer
    {
        $customer = $this->customerRepository->findById($customerId);
        if (!$customer) {
            throw new Exception($customerId . " Id'li Müşteri bulunamadı.");
        }

        return $customer;
    }

    /**
     * @param array $items
     * @return void
     * @throws Exception
     */
    private function validateStock(array $items): void
    {
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);
            if ($product->stock < $item['quantity']) {
                throw new Exception("Ürün '{$product->name}' için yeterli stok bulunmamaktadır.");
            }
        }
    }

    /**
     * @param array $data
     * @return Order
     */
    private function createNewOrder(array $data): Order
    {
        return $this->orderRepository->create($data);
    }

    /**
     * @param array $data
     * @return void
     */
    private function createNewOrderItem(array $data): void
    {
        $this->orderItemRepository->create($data);
    }

    /**
     * @param Order $order
     * @param array $items
     * @return void
     * @throws Exception
     */
    private function createOrderItemsAndUpdateStock(Order $order, array $items): void
    {
        try {
            foreach ($items as $item) {
                $product = $this->productRepository->findById($item['product_id']);
                if (!$product) {
                    throw new Exception("Ürün bulunamadı.");
                }

                $itemTotal = $product->price * $item['quantity'];

                $order->total += $itemTotal;

                $this->createNewOrderItem([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total' => $itemTotal,
                ]);

                $product->stock -= $item['quantity'];
                $product->save();
            }

            $order->save();
        } catch (Exception $e) {
            throw new Exception('Sipariş öğeleri oluşturulamadı: ' . $e->getMessage());
        }

    }

    /**
     * @param Order $order
     * @param Customer $customer
     * @return void
     * @throws Exception
     */
    private function applyDiscountAndUpdateRevenue(Order $order, Customer $customer): void
    {
        $this->discountService->applyDiscounts($order);
        $orderTotal = $order->discounted_total > 0 ? $order->discounted_total : $order->total;
        $this->updateCustomerRevenue($customer, $orderTotal);
    }

    /**
     * @param Customer $customer
     * @param float $total
     * @return void
     * @throws Exception
     */
    private function updateCustomerRevenue(Customer $customer, float $total): void
    {
        try {
            $customer->revenue += $total;
            $customer->save();

        } catch (Exception $e) {
            throw new Exception('Müşteri geliri güncellenemedi: ' . $e->getMessage());
        }
    }


    /**
     * @return array
     */
    public function getAllOrders(): array
    {
        $orders = $this->orderRepository->getOrders();
        return $this->getOrderMap($orders);
    }

    /**
     * @param $orders
     * @return array
     */
    private function getOrderMap($orders): array
    {
        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customerId' => $order->customer_id,
                'items' => $order->items->map(function ($item) {
                    return [
                        'productId' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unitPrice' => number_format($item->unit_price, 2),
                        'total' => number_format($item->total, 2),
                        'discountAmount' => number_format($item->discount_amount, 2),
                        'discountedTotal' => number_format($item->discounted_total, 2),
                    ];
                }),
                'total' => number_format($order->total, 2),
                'totalDiscount' => number_format($order->total_discount, 2),
                'discountedTotal' => number_format($order->discounted_total, 2),
            ];
        })->toArray();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function handleDeleteOrder(int $id): bool
    {
        $orderDelete = $this->orderRepository->delete($id);

        if (!$orderDelete) {
            Log::error('Order delete failed', ['error' => 'Order not found', 'order_id' => $id]);

            return false;
        }

        return true;
    }

}
