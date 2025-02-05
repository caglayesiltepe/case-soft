<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * @var CustomerService
     */
    private CustomerService $customerService;

    /**
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @OA\Get(
     *      path="/api/customer",
     *      operationId="getCustomerAll",
     *      tags={"Customer"},
     *      summary="Get all customers",
     *      description="Fetches a list of all customers.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="List of customers",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Irmak"),
     *                  @OA\Property(property="since", type="date", example="2025-01-01"),
     *                  @OA\Property(property="revenue", type="number", format="float", example=250.50),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Customers not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Müşteriler bulunamadı.")
     *          )
     *      )
     *  )
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $customers = $this->customerService->getAllCustomers();
            if (empty($customers)) {
                return response()->json(['error' => "Müşteriler bulunamadı"], 404);
            }
            return response()->json($customers, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/customer",
     *      operationId="createCustomer",
     *      tags={"Customer"},
     *      summary="Create an customer",
     *      description="Create a new customer and return the created customer.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "since","revenue"},
     *              @OA\Property(property="name", type="string", example="Irmak"),
     *              @OA\Property(property="since", type="date", example="2025-01-01"),
     *              @OA\Property(property="revenue", type="number", format="float", example=250.50),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Customer created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Müşteri başarıyla oluşturulmuştur."),
     *              @OA\Property(property="customer_id", type="integer", example=123),
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
     * @param CustomerCreateRequest $request
     * @return JsonResponse
     */
    public function store(CustomerCreateRequest $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        try {
            $customer = $this->customerService->handleCreateCustomer($request->validated());
            return response()->json([
                'message' => 'Müşteri başarıyla oluşturulmuştur.',
                'customer_id' => $customer->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer creation failed', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/customer/{id}",
     *      operationId="deleteCustomer",
     *      tags={"Customer"},
     *      summary="Delete an customer",
     *      description="Deletes an existing customer by ID.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Customer ID",
     *          @OA\Schema(type="integer", example=123)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Customer deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Müşteri silindi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Customer not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Müşteri Bulunamadı")
     *          )
     *      )
     *  )
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if ($this->customerService->handleDeleteCustomer($id)) {
                return response()->json(['message' => 'Müşteri silindi.']);
            }

            return response()->json(['error' => 'Müşteri bulunamadı.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
