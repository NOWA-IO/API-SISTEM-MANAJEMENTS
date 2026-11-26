<?php

use App\Enums\UserRoleEnum;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [UserController::class, 'login']);
        Route::post('register', [UserController::class, 'register']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [UserController::class, 'index']);
            Route::post('logout', [UserController::class, 'logout']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('users/list-data-paginate', [UserController::class, 'listPaginate']);
        Route::apiResource('users', UserController::class);
        

        Route::middleware(['role:' . UserRoleEnum::ADMIN->value])->group(function () {
            // route admin  
            Route::apiResource('employees', EmployeeController::class);
            Route::apiResource('products', ProductController::class);
            Route::apiResource('formulas', FormulaController::class);
        });

        Route::middleware(['role:' . UserRoleEnum::EMPLOYEE->value])->group(function () {
            // route employee
        });

        Route::middleware(['role:' . UserRoleEnum::SELLER->value])->group(function () {
            // route seller
        });
    });
});
