<?php

use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ImportProductsController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;





Route::group([
    'middleware' => ['auth:admin,web'],
    'prefix' => 'admin/dashboard',
    'as' => 'dashboard.',
], function () {


    # this way if I need the route of show before trash route.
    // Route::get('/categories/{category}', [CategoriesController::class, 'show'])
    //     ->name('categories.show')
    //     ->where('category', '\d+');

    # Here The arrangement it's very important
    // as if we put route trash after resource route will consider the route trash is show action.

    Route::get('/categories/trash', [CategoryController::class, 'trash'])
        ->name('categories.trash');

    Route::put('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->name('categories.restore');

    Route::delete('categories/{category}/force-delete', [CategoryController::class, 'forceDelete'])
        ->name('categories.force-delete');

    Route::get('/', [DashboardController::class, 'index'])
        // ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('products/import', [ImportProductsController::class, 'create'])->name('products.import');
    Route::post('products/import', [ImportProductsController::class, 'store']);

    Route::resources(
        [
            'products' => ProductsController::class,
            'categories' => CategoryController::class,
            'roles' => RolesController::class,
            'users' => UsersController::class,
            'admins' => AdminsController::class,
        ],
    );

    // Route::resource('categories', CategoryController::class);
    // Route::resource('categories', CategoryController::class)->except('show');

    // Route::resource('products', ProductsController::class);
});
