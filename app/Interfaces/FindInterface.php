<?php

namespace App\Interfaces;

interface FindInterface
{
    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): mixed;
}
