<?php

namespace App\Repositories;

use App\Helpers\QueryFilterHelper;
use App\Interfaces\EmployeeEarningInterface;
use App\Models\EmployeeEarning;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeEarningRepository extends BaseRepository implements EmployeeEarningInterface
{
    public function __construct(EmployeeEarning $model)
    {
        $this->model = $model;
    }

    /**
     * Get all employee earnings with filters, sorting, and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllData(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('user');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
            unset($filters['search']);
        }

        QueryFilterHelper::applyFilters($query, $filters);
        QueryFilterHelper::applySorting($query, $filters);

        return $query->paginate($perPage);
    }
}
