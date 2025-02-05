<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\userRepository;
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
     */
    public function createUser(array $request): ?User
    {
        return $this->userRepository->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($request['password']),
        ]);
    }

}
