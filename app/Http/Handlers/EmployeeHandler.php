<?php

namespace App\Http\Handlers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employe;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeHandler
{
    protected EmployeeRepository $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Map request data for storing employee.
     *
     * @param EmployeeRequest $request
     * @return array
     */
    public function mapStore(EmployeeRequest $request): array
    {
        $validated = $request->validated();
        $roleInfo = $this->resolveRole($request->input('role', UserRoleEnum::EMPLOYEE->value));

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $roleInfo['db'],
        ];

        return [
            'payload' => $data,
            'spatie_role' => $roleInfo['spatie'],
        ];
    }

    /**
     * Map request data for updating employee.
     *
     * @param EmployeeRequest $request
     * @param Employe $employee
     * @return array
     */
    public function mapUpdate(EmployeeRequest $request, Employe $employee): array
    {
        $validated = $request->validated();
        $data = [
            'name' => $validated['name'] ?? $employee->name,
            'email' => $validated['email'] ?? $employee->email,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $spatieRole = null;
        if ($request->filled('role')) {
            $roleInfo = $this->resolveRole($request->input('role'));
            $data['role'] = $roleInfo['db'];
            $spatieRole = $roleInfo['spatie'];
        }

        return [
            'payload' => $data,
            'spatie_role' => $spatieRole,
        ];
    }

    /**
     * Handle store employee and assign role (admin/employee).
     *
     * @param EmployeeRequest $request
     * @return Employe
     */
    public function handleStore(EmployeeRequest $request): Employe
    {
        $mapped = $this->mapStore($request);

        /** @var Employe $employee */
        $employee = $this->repository->store($mapped['payload']);

        $role = Role::firstOrCreate([
            'name' => $mapped['spatie_role'],
            'guard_name' => 'web',
        ]);
        $employee->assignRole($role);

        return $employee;
    }

    /**
     * Handle update employee and sync role if provided.
     *
     * @param EmployeeRequest $request
     * @param Employe $employee
     * @return Employe
     */
    public function handleUpdate(EmployeeRequest $request, Employe $employee): Employe
    {
        $mapped = $this->mapUpdate($request, $employee);

        $employee->update($mapped['payload']);

        if (!empty($mapped['spatie_role'])) {
            $role = Role::firstOrCreate([
                'name' => $mapped['spatie_role'],
                'guard_name' => 'web',
            ]);
            $employee->syncRoles([$role]);
        }

        return $employee->fresh();
    }

    /**
     * Resolve database role and Spatie role name.
     *
     * @param string $role
     * @return array{db: string, spatie: string}
     */
    protected function resolveRole(string $role): array
    {
        return match (strtolower($role)) {
            'admin' => [
                'db' => 'admin',
                'spatie' => UserRoleEnum::ADMIN->value,
            ],
            'seller' => [
                'db' => 'seller',
                'spatie' => UserRoleEnum::SELLER->value,
            ],
            default => [
                'db' => 'employee',
                'spatie' => UserRoleEnum::EMPLOYEE->value,
            ],
        };
    }
}

