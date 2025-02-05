<?php

namespace App\Repositories;

use App\Interfaces\CreateInterface;
use App\Models\User;

class UserRepository implements CreateInterface
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

}
