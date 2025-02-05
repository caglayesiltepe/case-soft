<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="userRegister",
     *      tags={"Auth"},
     *      summary="Create an user",
     *      description="Create a new user and return the created user.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="Çağla"),
     *              @OA\Property(property="email", type="string", example="caglayesiltepe@gmail.com"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *              @OA\Property(property="password_confirmation", type="string", example="123456"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Kullanıcı başarıyla oluşturuldu."),
     *               @OA\Property(property="user", type="array",
     *                       @OA\Items(
     *                           type="object",
     *                           @OA\Property(property="name", type="string", example="Çağla"),
     *                           @OA\Property(property="email", type="string", example="caglayesiltepe@gmail.com"),
     *                           @OA\Property(property="updated_at", type="date", example="2025-01-01"),
     *                           @OA\Property(property="created_at", type="date", example="2025-01-01"),
     *                           @OA\Property(property="id", type="integer", example=1),
     *                       )
     *                   ),
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
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function register(UserCreateRequest $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                'message' => "Kullanıcı başarıyla oluşturuldu.",
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User register failed', 'message' => $e->getMessage()], 422);
        }

    }

    /**
     * @OA\Post(
     *       path="/api/login",
     *       operationId="userLogin",
     *       tags={"Auth"},
     *       summary="User login",
     *       description="User login",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(
     *               required={"email","password"},
     *               @OA\Property(property="email", type="string", example="caglayesiltepe@gmail.com"),
     *               @OA\Property(property="password", type="string", example="123456"),
     *           )
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="User created successfully",
     *           @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="user", type="array",
     *                        @OA\Items(
     *                            type="object",
     *                            @OA\Property(property="id", type="integer", example=1),
     *                            @OA\Property(property="name", type="string", example="Çağla"),
     *                            @OA\Property(property="email", type="string", example="caglayesiltepe@gmail.com"),
     *                            @OA\Property(property="email_verified_at", type="date", example="2025-01-01"),
     *                            @OA\Property(property="created_at", type="date", example="2025-01-01"),
     *                            @OA\Property(property="updated_at", type="date", example="2025-01-01"),
     *                        )
     *                    ),
     *               @OA\Property(property="token", type="string", example="1|1231121321132211321321231")
     *           )
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Invalid input",
     *           @OA\JsonContent(
     *               @OA\Property(property="error", type="string", example="Invalid request data.")
     *           )
     *       )
     *   )
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return response()->json([
                'user' => $user,
                'token' => $user->createToken('MyApp')->plainTextToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User login failed', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="userLogout",
     *      tags={"Auth"},
     *      summary="Logout user",
     *      description="Logs out the user by deleting all their tokens.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successfully logged out",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logged out successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Logout failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logout failed"),
     *              @OA\Property(property="error", type="string", example="error message here")
     *          )
     *      )
     *  )
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

}
