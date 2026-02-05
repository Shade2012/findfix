<?php

namespace App\Providers;
use Illuminate\Support\Facades\Response;
use App\Interfaces\AuthRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Interfaces\BuildingRepositoryInterface;
use App\Repositories\BuildingRepository;
use App\Interfaces\ItemRepositoryInterface;
use App\Repositories\ItemRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class, 
            AuthRepository::class
        );
        $this->app->bind(
            BuildingRepositoryInterface::class, 
            BuildingRepository::class
        );
        $this->app->bind(
            ItemRepositoryInterface::class,
            ItemRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = null, $message = 'Success', $status = 200) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ], $status);
        });

        Response::macro('error', function ($message = 'Error', $errors = null, $status = 400) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });
    }
}
