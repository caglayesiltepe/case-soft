<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface GetAllInterface
{
    /**
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $page, int $perPage): LengthAwarePaginator;
}
