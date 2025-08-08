<?php

namespace App\Providers;

use App\Repositories\CategoryRepo;
use App\Repositories\Interfaces\ICategoryRepo;
use App\Repositories\Interfaces\IProductRepo;
use App\Repositories\Interfaces\IVariantGroupRepo;
use App\Repositories\Interfaces\IVariantOptionRepo;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
