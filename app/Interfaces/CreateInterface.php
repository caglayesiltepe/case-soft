<?php

namespace App\Interfaces;

interface CreateInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed;
}
