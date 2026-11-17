<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Http\Handlers\UserHandler;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Users\UserPaginateResource;
use App\Resources\Users\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $repository;
    protected $handler;

    public function __construct(UserRepository $repository, UserHandler $handler)
    {
        $this->repository = $repository;
        $this->handler = $handler;
    }

    /**
     * Get Current User using auth token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            return ResponseHelper::success(UserResource::make(auth()->user()), 'Profil pengguna berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $store = $this->handler->mapStore($request);
            $user = $this->repository->store($store);

            $role = Role::firstOrCreate(['name' => UserRoleEnum::EMPLOYEE->value]);
            $user->assignRole($role);

            DB::commit();
            return ResponseHelper::success(UserResource::make($user), 'Pengguna berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menambahkan pengguna: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return ResponseHelper::success(UserResource::make($this->repository->show($id)), 'Data pengguna berhasil diambil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $payload = $this->handler->mapUpdate($request, $user);
            $user->update($payload);

            DB::commit();
            return ResponseHelper::success(UserResource::make($user), 'Pengguna berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal memperbarui pengguna: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();

            DB::commit();
            return ResponseHelper::success(message: 'Pengguna berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(message: 'Gagal menghapus pengguna: ' . $th->getMessage());
        }
    }

    /**
     * Get list user with paginate
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listPaginate(Request $request): JsonResponse
    {
        try {
            $users = $this->repository->customPaginate($request, $request->paginate ?? 10);

            return ResponseHelper::success(
                UserPaginateResource::make($users, PaginateHelper::getPaginate($users)),
                'Data pengguna berhasil diambil.',
                pagination: true
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }

    /**
     * Handle registration for employee (Karyawan)
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $result = $this->handler->handleRegister($request);
            DB::commit();

            return ResponseHelper::success(
                $result,
                message: 'Registrasi karyawan berhasil.',
                code: 201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(
                message: 'Registrasi karyawan gagal: ' . $th->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Alias for register method (mendukung endpoint /registrasi)
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function registrasi(RegisterRequest $request): JsonResponse
    {
        return $this->register($request);
    }

    /**
     * Handle user login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->handler->handleSignIn($request);
            return ResponseHelper::success($result, message: 'Login berhasil.');
        } catch (\Throwable $th) {
            $code = $th->getCode();
            $statusCode = ($code >= 400 && $code < 600) ? (int)$code : 401;
            return ResponseHelper::error(message: $th->getMessage(), code: $statusCode);
        }
    }

    /**
     * Handle user logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
            return ResponseHelper::success(message: 'Logout berhasil.');
        } catch (\Throwable $th) {
            return ResponseHelper::error(message: $th->getMessage());
        }
    }
}
