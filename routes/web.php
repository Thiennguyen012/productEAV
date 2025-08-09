<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('showAll');
    Route::get('/{slug}', [ProductController::class, 'getProductDetail'])->name('detail');
});

Route::prefix('admin')->name('Admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::prefix('/products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('list');
        Route::get('/new', [AdminController::class, 'showNewProduct'])->name('showNew');
        Route::post('/new', [AdminProductController::class, 'newProduct'])->name('new');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
    });
});
