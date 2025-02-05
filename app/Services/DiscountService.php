<?php

namespace App\Services;

use App\Models\Order;
use App\Enum\DiscountType;
use App\Models\OrderDiscount;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use Exception;

class DiscountService
{
    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function applyDiscounts(Order $order): void
    {
        if ($order->total >= 1000) {
            $this->applyOrderTotalDiscount($order);
        }

        $this->applyCategory2Discount($order);
        $this->applyCategory1Discount($order);
    }

    /**
     * @param Order $order
     * @return void
     */
    private function applyOrderTotalDiscount(Order $order): void
    {
        $discountAmount = $order->total * 0.10;
        $this->orderDiscountedUpdate($order, $discountAmount);

        foreach ($order->items as $item) {
            $itemDiscount = ($order->total > 0)
                ? ($item->total / $order->total) * $discountAmount
                : 0;
            $this->itemDiscountedUpdate($item, $itemDiscount);
        }

        $this->createDiscount([
            'order_id' => $order->id,
            'discount_reason' => DiscountType::OVER_1000->value,
            'discount_amount' => $discountAmount,
            'subtotal' => $order->discounted_total,
        ]);

    }

    /**
     * @param Order $order
     * @return void
     */
    private function applyCategory2Discount(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product->category == 2 && $item->quantity >= 6) {
                $discountAmount = $item->unit_price;
                $this->orderDiscountedUpdate($order, $discountAmount);
                $this->itemDiscountedUpdate($item, $discountAmount);

                $this->createDiscount([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'discount_reason' => DiscountType::BUY_5_GET_1->value,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $order->discounted_total,
                ]);
            }
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    private function applyCategory1Discount(Order $order): void
    {
        $category1Items = $order->items->filter(fn($item) => $item->product->category == 1);

        if ($category1Items->count() >= 2) {
            $item = $category1Items->sortBy('total')->first();
            $discountAmount = $item->total * 0.20;

            $this->orderDiscountedUpdate($order, $discountAmount);
            $this->itemDiscountedUpdate($item, $discountAmount);

            $this->createDiscount([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'discount_reason' => 'CATEGORY_1_DISCOUNT',
                'discount_amount' => $discountAmount,
                'subtotal' => $order->discounted_total,
            ]);
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function createDiscount(array $data): void
    {
        OrderDiscount::create($data);
    }

    /**
     * @param Order $order
     * @param float $discountAmount
     * @return void
     */
    private function orderDiscountedUpdate(Order $order, float $discountAmount): void
    {
        $order->discounted_total = max(0, $order->discounted_total > 0
            ? $order->discounted_total - $discountAmount
            : $order->total - $discountAmount);

        $order->total_discount += $discountAmount;
        $order->save();
    }

    /**
     * @param OrderItem $item
     * @param float $discountAmount
     * @return void
     */
    private function itemDiscountedUpdate(OrderItem $item, float $discountAmount): void
    {
        $item->discount_amount += $discountAmount;
        $item->discounted_total = max(0, $item->discounted_total > 0
            ? $item->discounted_total - $discountAmount
            : $item->total - $discountAmount);
        $item->save();
    }

    /**
     * @param int $orderId
     * @return array
     * @throws Exception
     */
    public function getOrderDiscounts(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);
        if($order){
            return $this->getOrderDiscountMap($order);
        }

        throw new Exception("Sipariş bulunamadı");
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getOrderDiscountMap(Order $order): array
    {
        return [
            'id' => $order->id,
            'discounts' => $order->discounts->map(function ($item) {
                return [
                    'discountReason' => $item->discount_reason,
                    'discountAmount' => number_format($item->discount_amount, 2),
                    'subtotal' => number_format($item->subtotal, 2),
                ];
            })->toArray(),
            'total' => number_format($order->total, 2),
            'totalDiscount' => number_format($order->total_discount, 2),
            'discountedTotal' => number_format($order->discounted_total, 2),
        ];
    }

}
