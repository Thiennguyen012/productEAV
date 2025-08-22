<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('showAll');
    Route::get('/{slug}', [ProductController::class, 'getProductDetail'])->name('detail');
    Route::post('/{slug}', [CartController::class, 'addToCart'])->name('addCart');
});


Route::prefix('/cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'showCart'])->name('show');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::put('/update/{id}', [CartController::class, 'updateItem'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'deleteCartItem'])->name('remove');
});

Route::prefix('/checkout')->name('checkout.')->group(function () {
    Route::get('/', [OrderController::class, 'showOrder'])->name('show');
    Route::post('/', [OrderController::class, 'placeOrder'])->name('placeOrder');
});

Route::prefix('admin')->name('Admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::prefix('/categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'showAll'])->name('list');
        Route::get('/new', [AdminCategoryController::class, 'showNewCategory'])->name('showNew');
        Route::post('/new', [AdminCategoryController::class, 'newCategory'])->name('new');
        Route::get('/{id}', [AdminCategoryController::class, 'getCategoryById']);
        Route::put('/{id}', [AdminCategoryController::class, 'updateCategory'])->name('update');
        Route::delete('/{id}', [AdminCategoryController::class, 'deleteCategory'])->name('delete');
    });
    Route::prefix('/products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('list');
        Route::get('/new', [AdminController::class, 'showNewProduct'])->name('showNew');
        Route::post('/new', [AdminProductController::class, 'newProduct'])->name('new');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
    });
    Route::prefix('/order')->name('order.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'getAll']);
        Route::get('/{id}',[AdminOrderController::class, 'getOrderById']);
        Route::put('/{id}',[AdminOrderController::class, 'updateOrder']);
    });
});
