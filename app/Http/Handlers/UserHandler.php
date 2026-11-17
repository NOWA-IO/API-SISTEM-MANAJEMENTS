<?php

namespace App\Http\Handlers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Resources\Users\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserHandler
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mapStore(UserRequest $request): array
    {
        $data = $request->validated();

        if ($request->password) {
            $data["password"] = $request->password;
        }

        return $data;
    }

    public function mapUpdate(UserRequest $request, User $user): array
    {
        $data = $request->validated();

        if ($request->password) {
            $data["password"] = $request->password;
        }

        return $data;
    }

    /**
     * Handle registration for employee
     *
     * @param RegisterRequest $request
     * @return array
     */
    public function handleRegister(RegisterRequest $request): array
    {
        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'employee',
        ];

        $user = $this->repository->store($payload);

        // Assign Spatie Role Employee
        $role = Role::firstOrCreate(['name' => UserRoleEnum::EMPLOYEE->value]);
        $user->assignRole($role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => UserResource::make($user),
            'token' => $token,
            'role' => UserRoleEnum::EMPLOYEE->value,
        ];
    }

    /**
     * Handle sign in via api
     *
     * @param LoginRequest $request
     * @return array
     */
    public function handleSignIn(LoginRequest $request): array
    {
        $validated = $request->validated();

        $remember = false;
        if (isset($validated["remember_me"])) {
            $remember = $validated["remember_me"];
            unset($validated["remember_me"]);
        }

        if (!Auth::attempt($validated, $remember)) {
            throw new \Exception(trans('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        return [
            'user' => UserResource::make($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
            'role' => $user->roles->pluck('name')->first() ?? $user->role,
        ];
    }
}
