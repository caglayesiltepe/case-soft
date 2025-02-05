<?php

namespace App\Http\Controllers;

use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    /**
     * @var DiscountService
     */
    private DiscountService $discountService;

    /**
     * @param DiscountService $discountService
     */
    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * @OA\Get(
     *      path="/api/discounted/{orderId}",
     *      operationId="orderDiscounted",
     *      tags={"Discounted"},
     *      summary="Get order discounts",
     *      description="Fetch order discounts for the given order ID.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="orderId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              example=36
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order discounts fetched successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=36),
     *              @OA\Property(
     *                  property="discounts",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="discountReason", type="string", example="10_PERCENT_OVER_1000"),
     *                      @OA\Property(property="discountAmount", type="string", example="181.53"),
     *                      @OA\Property(property="subtotal", type="string", example="1,633.77")
     *                  )
     *              ),
     *              @OA\Property(property="total", type="string", example="1,815.30"),
     *              @OA\Property(property="totalDiscount", type="string", example="291.81"),
     *              @OA\Property(property="discountedTotal", type="string", example="1,523.49")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Invalid order ID"),
     *      @OA\Response(response=422, description="Order not found")
     *  )
     * @param int $orderId
     * @return JsonResponse
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            if (!is_numeric($orderId) || $orderId <= 0) {
                return response()->json(['error' => 'Order Id Integer olmadlıdır'], 400);
            }

            $orderDiscounted = $this->discountService->getOrderDiscounts($orderId);
            return response()->json($orderDiscounted, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order creation failed', 'message' => $e->getMessage()], 422);
        }
    }
}
