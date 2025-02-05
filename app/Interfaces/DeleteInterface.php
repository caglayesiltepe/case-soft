<?php

namespace App\Interfaces;

interface DeleteInterface
{
    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
