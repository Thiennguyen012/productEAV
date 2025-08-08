<?php

namespace App\Providers;

use App\Services\CategoryService;
use App\Services\Interfaces\ICategoryService;
use App\Services\Interfaces\IProductService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IProductService::class, ProductService::class);
        $this->app->bind(ICategoryService::class, CategoryService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
