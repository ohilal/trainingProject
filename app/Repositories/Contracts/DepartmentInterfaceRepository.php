<?php

namespace App\Repositories\Contracts;

interface DepartmentInterfaceRepository
{
    /**
     * getAllByTitleAndId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByTitleAndId();

    /**
     * Get all departments.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
}
