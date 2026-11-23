<?php

namespace App\Http\Controllers;

use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Handlers\EmployeeHandler;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employe;
use App\Repositories\EmployeeRepository;
use App\Resources\Employees\EmployeePaginateResource;
use App\Resources\Employees\EmployeeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected EmployeeRepository $repository;
    protected EmployeeHandler $handler;

    public function __construct(EmployeeRepository $repository, EmployeeHandler $handler)
    {
        $this->repository = $repository;
        $this->handler = $handler;
    }

    /**
     * Display a listing of employees.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $employees = $this->repository->get();
            return ResponseHelper::success(EmployeeResource::collection($employees), 'Data karyawan berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Get list employee with paginate.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listPaginate(Request $request): JsonResponse
    {
        try {
            $employees = $this->repository->customPaginate($request, $request->paginate ?? 10);

            return ResponseHelper::success(
                EmployeePaginateResource::make($employees, PaginateHelper::getPaginate($employees)),
                'Data karyawan berhasil diambil.',
                pagination: true
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Store a newly created employee (can assign admin / employee role).
     *
     * @param EmployeeRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $employee = $this->handler->handleStore($request);

            DB::commit();
            return ResponseHelper::success(EmployeeResource::make($employee), 'Karyawan berhasil ditambahkan.', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menambahkan karyawan: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified employee.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $employee = $this->repository->show($id);
            return ResponseHelper::success(EmployeeResource::make($employee), 'Data karyawan berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: 'Data karyawan tidak ditemukan: ' . $th->getMessage(), code: 404);
        }
    }

    /**
     * Update the specified employee.
     *
     * @param EmployeeRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(EmployeeRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            /** @var Employe $employee */
            $employee = $this->repository->show($id);
            $updatedEmployee = $this->handler->handleUpdate($request, $employee);

            DB::commit();
            return ResponseHelper::success(EmployeeResource::make($updatedEmployee), 'Karyawan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal memperbarui karyawan: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified employee from storage.
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
            return ResponseHelper::success(message: 'Karyawan berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menghapus karyawan: ' . $th->getMessage());
        }
    }
}

