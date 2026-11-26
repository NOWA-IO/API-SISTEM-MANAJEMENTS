<?php

namespace App\Repositories;

use App\Helpers\QueryFilterHelper;
use App\Interfaces\ProductInterface;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductInterface
{
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * Get all products with filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('user');
        $searchColumn = ['name'];

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        QueryFilterHelper::applyFilters($query, $filters, $searchColumn);
        QueryFilterHelper::applySorting($query, $filters);

        return $query->paginate($perPage);
    }
}
