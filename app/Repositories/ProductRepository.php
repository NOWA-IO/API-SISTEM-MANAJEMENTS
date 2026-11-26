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
<<<<<<< HEAD
     * Get all products with filters and pagination.
=======
     * Get all products with filters, sorting, and pagination.
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
<<<<<<< HEAD
    public function getAllProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
=======
    public function getAllProduct(array $filters = [], int $perPage = 10): LengthAwarePaginator
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
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
