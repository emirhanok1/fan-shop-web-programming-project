<?php

use Illuminate\Support\Facades\Route;

// User Side Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;

// Admin Side Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Public Routes (No Auth required)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// 2. Authenticated User Routes (Middleware: auth)
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/deactivate', [UserProfileController::class, 'deactivate'])->name('profile.deactivate');

    // Shopping Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
});

// 3. Admin Routes (Middleware: auth, admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('products.toggle');
    Route::delete('/products/images/{productImage}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('/tmdb-search', [AdminProductController::class, 'tmdbSearch'])->name('tmdb.search');

    // Users Management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/advance', [AdminOrderController::class, 'advance'])->name('orders.advance');
    Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
});

if (app()->environment('local', 'testing')) {
    Route::get('/test-weather/{city}', function ($city) {
        $weather = app(\App\Services\WeatherService::class)
            ->getByCity($city);
        return response()->json($weather);
    });

    Route::get('/test-tmdb/{type}/{id}', function ($type, $id) {
        $tmdb = app(\App\Services\TMDBService::class)
            ->getById($id, $type);
        return response()->json($tmdb);
    });
}

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return 'Cache cleared successfully!';
});

Route::get('/debug-tmdb', function () {
    $key = config('services.tmdb.key');
    $envKey = env('TMDB_API_KEY');
    
    $info = [
        'config_key_exists' => !empty($key),
        'config_key_length' => strlen($key),
        'config_key_start' => substr($key, 0, 10),
        'config_key_end' => substr($key, -10),
        'env_key_exists' => !empty($envKey),
        'env_key_length' => strlen($envKey),
        'env_key_start' => substr($envKey, 0, 10),
        'env_key_end' => substr($envKey, -10),
        'app_env' => app()->environment(),
    ];
    
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Accept' => 'application/json',
            ])
            ->get('https://api.themoviedb.org/3/search/multi', [
                'query' => 'The Boys',
                'language' => 'tr-TR',
                'page' => 1,
            ]);
            
        $info['tmdb_response_status'] = $response->status();
        $info['tmdb_response_successful'] = $response->successful();
        $info['tmdb_response_body'] = $response->json();
    } catch (\Exception $e) {
        $info['error'] = $e->getMessage();
    }
    
    return response()->json($info);
});

require __DIR__.'/auth.php';
