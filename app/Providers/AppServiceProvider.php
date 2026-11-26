<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            \App\Interfaces\UserInterface::class => \App\Repositories\UserRepository::class,
            \App\Interfaces\EmployeInterface::class => \App\Repositories\EmployeeRepository::class,
            \App\Interfaces\EmployeeInterface::class => \App\Repositories\EmployeeRepository::class,
            \App\Interfaces\ProductInterface::class => \App\Repositories\ProductRepository::class,
            \App\Interfaces\FormulaInterface::class => \App\Repositories\FormulaRepository::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('app.maximal_request', 60))
                ->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
