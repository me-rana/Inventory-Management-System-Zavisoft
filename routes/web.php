<?php

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [BackendController::class, 'dashboard'])->name('dashboard');

    Route::prefix('auth')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['create','edit','show']);
        Route::resource('products', ProductController::class)->except(['create','edit','show']);
        Route::resource('orders', OrderController::class);
    });
});
