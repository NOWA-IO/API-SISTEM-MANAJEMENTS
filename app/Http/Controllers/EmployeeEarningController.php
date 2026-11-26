<?php

namespace App\Http\Controllers;

use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\EmployeeEarningRequest;
use App\Repositories\EmployeeEarningRepository;
use App\Resources\EmployeeEarnings\EmployeeEarningPaginateResource;
use App\Resources\EmployeeEarnings\EmployeeEarningResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeEarningController extends Controller
{
    protected EmployeeEarningRepository $repository;

    public function __construct(EmployeeEarningRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of employee earnings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $filters = [
                'search' => $request->input('search', $request->input('q', $request->input('keyword'))),
                'user_id' => $request->input('user_id'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', $request->input('sort_dir', 'desc')),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ];

            $earnings = $this->repository->getAllData($filters, $perPage);

            return ResponseHelper::success(
                EmployeeEarningPaginateResource::make($earnings, PaginateHelper::getPaginate($earnings)),
                'Data penghasilan karyawan berhasil diambil.',
                pagination: true
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Store a newly created employee earning in storage.
     *
     * @param EmployeeEarningRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeEarningRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $earning = $this->repository->store($request->validated());
            $earning->load('user');

            DB::commit();
            return ResponseHelper::success(
                EmployeeEarningResource::make($earning),
                'Penghasilan karyawan berhasil ditambahkan.',
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menambahkan penghasilan karyawan: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified employee earning.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $earning = $this->repository->show($id);
            $earning->load('user');

            return ResponseHelper::success(
                EmployeeEarningResource::make($earning),
                'Data penghasilan karyawan berhasil diambil.'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: 'Data penghasilan karyawan tidak ditemukan: ' . $th->getMessage(), code: 404);
        }
    }

    /**
     * Update the specified employee earning in storage.
     *
     * @param EmployeeEarningRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(EmployeeEarningRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $earning = $this->repository->show($id);
            $this->repository->update($id, $request->validated());
            $earning->refresh()->load('user');

            DB::commit();
            return ResponseHelper::success(
                EmployeeEarningResource::make($earning),
                'Penghasilan karyawan berhasil diperbarui.'
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal memperbarui penghasilan karyawan: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified employee earning from storage.
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
            return ResponseHelper::success(message: 'Penghasilan karyawan berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menghapus penghasilan karyawan: ' . $th->getMessage());
        }
    }
}
