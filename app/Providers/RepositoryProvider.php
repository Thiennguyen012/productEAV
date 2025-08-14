<?php

namespace App\Providers;

use App\Repositories\CartItemRepo;
use App\Repositories\CartRepo;
use App\Repositories\CategoryRepo;
use App\Repositories\Interfaces\ICartItemRepo;
use App\Repositories\Interfaces\ICartRepo;
use App\Repositories\Interfaces\ICategoryRepo;
use App\Repositories\Interfaces\IOrderItemRepo;
use App\Repositories\Interfaces\IOrderRepo;
use App\Repositories\Interfaces\IProductRepo;
use App\Repositories\Interfaces\IVariantGroupRepo;
use App\Repositories\Interfaces\IVariantOptionRepo;
use App\Repositories\OrderItemRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepo;
use App\Repositories\VariantGroupRepo;
use App\Repositories\VariantOptionRepo;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IProductRepo::class, ProductRepo::class);
        $this->app->bind(IVariantGroupRepo::class, VariantGroupRepo::class);
        $this->app->bind(IVariantOptionRepo::class, VariantOptionRepo::class);
        $this->app->bind(ICategoryRepo::class, CategoryRepo::class);
        $this->app->bind(ICartRepo::class, CartRepo::class);
        $this->app->bind(ICartItemRepo::class, CartItemRepo::class);
        $this->app->bind(IOrderRepo::class, OrderRepo::class);
        $this->app->bind(IOrderItemRepo::class, OrderItemRepo::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
