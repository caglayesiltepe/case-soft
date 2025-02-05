<?php

namespace App\Services;

use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use App\Repositories\userRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @var userRepository
     */
    private UserRepository $userRepository;

    /**
     * @param userRepository $userRepository
     */
    public function __construct(
        userRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $request
     * @return User|null
     * @throws Exception
     */
    public function createUser(array $request): ?User
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->create([
                'name' => $request['name'],
                'email' => $request['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($request['password']),
            ]);

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            throw new Exception('User creation failed: ' . $e->getMessage());
        }
    }

}
