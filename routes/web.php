<?php

use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\CurrencyConvertorController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PaymentsController;
use App\Http\Controllers\Front\ProductsController;
use App\Http\Controllers\Front\TwoFactorAuthenticationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group([
    // 'prefix' => "{locale}",
    'prefix' => LaravelLocalization::setLocale(),

], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');

    Route::get('/products', [ProductsController::class, 'index'])
        ->name('products.index');

    Route::get('/products/{product}', [ProductsController::class, 'show'])
        ->name('products.show');

    Route::resource('cart', CartController::class);

    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout');

    Route::get('auth/user/2fa', [TwoFactorAuthenticationController::class, 'index'])
        ->name('front.2fa');

    Route::post('currency', [CurrencyConvertorController::class, 'store'])
        ->name('currency.store');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});


Route::get('auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])
    ->name('auth.socialite.redirect');

Route::get('auth/{provider}/callback', [SocialLoginController::class, 'callback'])
    ->name('auth.socialite.callback');

Route::get('auth/{provider}/user', [SocialController::class, 'index']);

Route::get('orders/{order}/pay', PaymentsController::class, 'create')
    ->name('orders.payments.create');

// here I made comment for this lin to transfer from routes breeze to fortify.
// require __DIR__ . '/auth.php';

require __DIR__ . '/dashboard.php';
