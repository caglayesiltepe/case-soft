<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *      path="/api/order",
     *      operationId="getAllOrders",
     *      tags={"Order"},
     *      summary="Get all orders",
     *      description="Fetches a list of all orders.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="List of orders",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="customer_id", type="integer", example=123),
     *                  @OA\Property(property="items", type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="productId", type="integer", example=100),
     *                          @OA\Property(property="quantity", type="integer", example=2),
     *                          @OA\Property(property="unitPrice", type="string", example="10.50"),
     *                          @OA\Property(property="total", type="string", example="21.00"),
     *                          @OA\Property(property="discountAmount", type="string", example="2.00"),
     *                          @OA\Property(property="discountedTotal", type="string", example="19.00")
     *                      )
     *                  ),
     *                  @OA\Property(property="total", type="number", format="float", example=250.50),
     *                  @OA\Property(property="totalDiscount", type="number", format="float", example=0.00),
     *                  @OA\Property(property="discountedTotal", type="number", format="float", example=0.00),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Orders not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Siparişler bulunamadı.")
     *          )
     *      )
     *  )
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrders();
            if (empty($orders)) {
                return response()->json(['error' => "Siparişler bulunamadı"], 404);
            }
            return response()->json($orders, 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/order",
     *     operationId="createOrder",
     *     tags={"Order"},
     *     summary="Create an order",
     *     description="Create a new order and return the created order.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id", "items"},
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=100),
     *                     @OA\Property(property="quantity", type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Siparişiniz başarıyla oluşturulmuştur."),
     *             @OA\Property(property="order_id", type="integer", example=123),
     *             @OA\Property(property="total", type="number", format="float", example=120.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid request data.")
     *         )
     *     )
     * )
     * @param OrderCreateRequest $request ,
     * @return JsonResponse
     */

    public function store(OrderCreateRequest $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        try {
            $order = $this->orderService->handleCreateOrder($request->validated());
            return response()->json([
                'message' => 'Siparişiniz başarıyla oluşturulmuştur.',
                'order_id' => $order->id,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Order creation failed', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/order/{id}",
     *     operationId="deleteOrder",
     *     tags={"Order"},
     *     summary="Delete an order",
     *     description="Deletes an existing order by ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sipariş silindi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sipariş Bulunamadı")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if ($this->orderService->handleDeleteOrder($id)) {
                return response()->json(['message' => 'Sipariş silindi']);
            }
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
