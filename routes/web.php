<?php

use App\Http\Controllers\Auth\RoleSelectionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// login, register, logout and password reset (laravel/ui)
Auth::routes();
Route::get('/register/role', [RoleSelectionController::class, 'show'])->name('register.role');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');

Route::get('/customer/dashboard', [DashboardController::class, 'customer'])->name('customer.dashboard');
Route::get('/vendor/dashboard', [DashboardController::class, 'vendor'])->name('vendor.dashboard');
Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/superadmin', [DashboardController::class, 'superAdmin'])->name('superadmin.dashboard');
