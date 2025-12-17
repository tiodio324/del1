<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Catalog routes
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/product/{id}', [CatalogController::class, 'show'])->name('product.show');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Order routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.post');
    Route::get('/order-confirmation/{order_id}', [OrderController::class, 'confirmation'])->name('order.confirmation');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

// Manager dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/manager', function () {
        $user = auth()->user();
        if ($user->role !== 'manager') {
            abort(403);
        }
        return view('manager.dashboard');
    })->name('manager.dashboard');
});

// Support dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/support', function () {
        $user = auth()->user();
        if ($user->role !== 'support') {
            abort(403);
        }
        return view('support.dashboard');
    })->name('support.dashboard');
});
