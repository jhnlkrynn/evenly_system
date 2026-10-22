<?php

use App\Http\Controllers\Auth\RoleSelectionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// login, register, logout and password reset (laravel/ui)
Auth::routes();
Route::get('/register/role', [RoleSelectionController::class, 'show'])->middleware('guest')->name('register.role');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
Route::get('/vendors/{vendor}/booked-dates', [VendorController::class, 'bookedDates'])->name('vendors.booked-dates');

// Every signed-in role
Route::middleware(['auth', 'role:customer,vendor,admin,superadmin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Booking page + PDFs: the booking's customer, its vendor and admins (BookingPolicy).
    Route::get('/bookings/{booking:booking_code}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking:booking_code}/slip.pdf', [BookingDocumentController::class, 'slip'])->name('bookings.slip');
    Route::get('/bookings/{booking:booking_code}/invoice.pdf', [BookingDocumentController::class, 'invoice'])->name('bookings.invoice');
});

Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');

    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking:booking_code}/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bookings/{booking:booking_code}/payments', [PaymentController::class, 'store'])->name('payments.store');
});

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'vendor'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
});

Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/', [DashboardController::class, 'superAdmin'])->name('dashboard');
});
