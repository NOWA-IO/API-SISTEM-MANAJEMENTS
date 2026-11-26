<?php

namespace App\Repositories;

use App\Helpers\QueryFilterHelper;
use App\Interfaces\FormulaInterface;
use App\Models\Formula;
use Illuminate\Pagination\LengthAwarePaginator;

class FormulaRepository extends BaseRepository implements FormulaInterface
{
    public function __construct(Formula $formula)
    {
        $this->model = $formula;
    }

    /**
     * Get all formulas with filters, sorting, and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllFormula(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('product');
        $searchColumn = ['description'];

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        QueryFilterHelper::applyFilters($query, $filters, $searchColumn);
        QueryFilterHelper::applySorting($query, $filters);

        return $query->paginate($perPage);
    }
}
