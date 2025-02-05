<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *       path="/api/product",
     *       operationId="getAllProducts",
     *       tags={"Product"},
     *       summary="Get all products",
     *       description="Fetches a list of all products with pagination.",
     *       security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="page",
     *           in="query",
     *           description="Page number",
     *           required=false,
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Number of items per page",
     *           required=false,
     *           @OA\Schema(type="integer", example=10)
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="List of products",
     *           @OA\JsonContent(
     *               type="array",
     *               @OA\Items(
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="name", type="string", example="Ürün 1"),
     *                   @OA\Property(property="category", type="number", example=1),
     *                   @OA\Property(property="price", type="number", format="float", example=10.00),
     *                   @OA\Property(property="stock", type="number" ,example=10)
     *               ),
     *               @OA\Property(property="total", type="integer", example=100),
     *               @OA\Property(property="per_page", type="integer", example=10),
     *               @OA\Property(property="last_page", type="integer", example=10)
     *           )
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="Products not found",
     *           @OA\JsonContent(
     *               @OA\Property(property="error", type="string", example="Ürünler bulunamadı.")
     *           )
     *       )
     *   )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $product = $this->productService->getAllProducts($page, $perPage);
            if (empty($product)) {
                return response()->json(['error' => "Ürünler bulunamadı"], 404);
            }
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/product",
     *      operationId="createProduct",
     *      tags={"Product"},
     *      summary="Create an product",
     *      description="Create a new product and return the created product.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "category", "price", "stock"},
     *              @OA\Property(property="name", type="string", example="Ürün 1"),
     *              @OA\Property(property="category", type="integer", example=1),
     *              @OA\Property(property="price", type="number", format="float", example=10.00),
     *              @OA\Property(property="stock", type="integer", example=10)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ürün başarıyla oluşturulmuştur."),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      required={"name", "category", "stock", "price", "updated_at", "created_at","id"},
     *                    @OA\Property(property="name", type="string", example="Ürün 1"),
     *                    @OA\Property(property="category", type="integer", example=1),
     *                    @OA\Property(property="price", type="number", format="float", example=10.00),
     *                    @OA\Property(property="stock", type="integer", example=10),
     *                    @OA\Property(property="created_at", type="date", example="2025-01-01"),
     *                    @OA\Property(property="updated_at", type="date", example="2025-01-01"),
     *                    @OA\Property(property="id", type="integer", example=10)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid input",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Invalid request data.")
     *          )
     *      )
     *  )
     *
     * @param ProductCreateRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(ProductCreateRequest $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }
        DB::beginTransaction();
        try {
            $product = $this->productService->productCreate($request->validated());
            DB::commit();
            return response()->json([
                'message' => 'Ürün başarıyla oluşturulmuştur.',
                'data' => $product->toArray()
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed', ['error' => $e->getMessage()]);
            throw new Exception('Product creation failed' . $e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/product/{id}",
     *      operationId="deleteProduct",
     *      tags={"Product"},
     *      summary="Delete an product",
     *      description="Deletes an existing product by ID.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="integer", example=123)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Ürün silindi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Ürün Bulunamadı")
     *          )
     *      )
     *  )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if ($this->productService->deleteProduct($id)) {
                return response()->json(['message' => 'Ürün silindi']);
            }

            Log::error('Product delete failed', ['error' => 'Product not found', 'product_id' => $id]);
            return response()->json(['message' => 'Ürün bulunamadı'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
