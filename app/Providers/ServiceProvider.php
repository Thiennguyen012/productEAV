<?php

namespace App\Providers;

use App\Services\CartItemService;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\Interfaces\ICartItemService;
use App\Services\Interfaces\ICartService;
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
        $this->app->bind(ICartService::class, CartService::class);
        $this->app->bind(ICartItemService::class, CartItemService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
