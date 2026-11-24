<?php

namespace App\Http\Controllers;

use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\FormulaRequest;
use App\Repositories\FormulaRepository;
use App\Resources\Formulas\FormulaPaginateResource;
use App\Resources\Formulas\FormulaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulaController extends Controller
{
    protected FormulaRepository $repository;

    public function __construct(FormulaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of formulas with filters, sorting, and pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = $request->only([
                'sort_by',
                'sort_dir',
                'search',
                'product_id',
                'date_from',
                'date_to',
            ]);

            $formulas = $this->repository->getAllFormula($filters, $perPage);

            return ResponseHelper::success(
                FormulaPaginateResource::make($formulas, PaginateHelper::getPaginate($formulas)),
                'Data formula berhasil diambil.'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Store a newly created formula in storage.
     *
     * @param FormulaRequest $request
     * @return JsonResponse
     */
    public function store(FormulaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $formula = $this->repository->store($request->validated());
            $formula->load('product');

            DB::commit();
            return ResponseHelper::success(FormulaResource::make($formula), 'Formula berhasil ditambahkan.', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menambahkan formula: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified formula.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $formula = $this->repository->show($id);
            $formula->load('product');

            return ResponseHelper::success(FormulaResource::make($formula), 'Data formula berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: 'Data formula tidak ditemukan: ' . $th->getMessage(), code: 404);
        }
    }

    /**
     * Update the specified formula in storage.
     *
     * @param FormulaRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(FormulaRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $formula = $this->repository->show($id);
            $formula->update($request->validated());

            DB::commit();
            return ResponseHelper::success(FormulaResource::make($formula->fresh()->load('product')), 'Formula berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal memperbarui formula: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified formula from storage.
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
            return ResponseHelper::success(message: 'Formula berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menghapus formula: ' . $th->getMessage());
        }
    }
}
