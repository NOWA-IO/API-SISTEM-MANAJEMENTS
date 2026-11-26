<?php

namespace App\Http\Controllers;

use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use App\Resources\Products\ProductPaginateResource;
use App\Resources\Products\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
<<<<<<< HEAD
     * Display a listing of products.
=======
     * Display a listing of products with filters, sorting, and pagination.
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
<<<<<<< HEAD
            $perPage = (int) $request->get('per_page', 10);
            $filters = [
                'search' => $request->input('search', $request->input('q', $request->input('keyword'))),
                'category' => $request->input('category'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', $request->input('sort_dir', 'desc')),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ];

            $products = $this->repository->getAllProducts($filters, $perPage);

            return ResponseHelper::success(
                ProductPaginateResource::make($products, PaginateHelper::getPaginate($products)),
                'Data barang berhasil diambil.',
                pagination: true
=======
            $perPage = $request->get('per_page', 10);
            $filters = $request->only([
                'sort_by',
                'sort_dir',
                'search',
                'category',
                'date_from',
                'date_to',
            ]);

            $products = $this->repository->getAllProduct($filters, $perPage);

            return ResponseHelper::success(
                ProductPaginateResource::make($products, PaginateHelper::getPaginate($products)),
                'Data barang berhasil diambil.'
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Store a newly created product in storage.
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $payload = $request->validated();
<<<<<<< HEAD
            $payload['user_id'] = Auth::id();
=======
            $payload['user_id'] = auth()->id();
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377

            $product = $this->repository->store($payload);

            DB::commit();
            return ResponseHelper::success(ProductResource::make($product), 'Barang berhasil ditambahkan.', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menambahkan barang: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified product.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->repository->show($id);
            $product->load('user');

            return ResponseHelper::success(ProductResource::make($product), 'Data barang berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: 'Data barang tidak ditemukan: ' . $th->getMessage(), code: 404);
        }
    }

    /**
     * Update the specified product in storage.
     *
     * @param ProductRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(ProductRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $product = $this->repository->show($id);
<<<<<<< HEAD
            $this->repository->update($id, $request->validated());
            $product->refresh()->load('user');

            DB::commit();
            return ResponseHelper::success(ProductResource::make($product), 'Barang berhasil diperbarui.');
=======
            $product->update($request->validated());

            DB::commit();
            return ResponseHelper::success(ProductResource::make($product->fresh()->load('user')), 'Barang berhasil diperbarui.');
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal memperbarui barang: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->repository->delete($id);

            DB::commit();
            return ResponseHelper::success(message: 'Barang berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menghapus barang: ' . $th->getMessage());
        }
    }
}
