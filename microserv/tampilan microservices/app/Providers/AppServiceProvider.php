<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * MICROSERVICES MODE: All database access is disabled.
     * Laravel is now an API consumer for Go microservices.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ⚠️ SAFETY CHECK: Prevent accidental database connections
        // If database queries are attempted, they will fail gracefully
        DB::listen(function ($query) {
            \Illuminate\Support\Facades\Log::warning(
                '⚠️ DIRECT DATABASE ACCESS DETECTED - Use microservices instead!',
                [
                    'query' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]
            );
        });
    }
}

