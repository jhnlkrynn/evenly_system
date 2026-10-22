<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RoleSelectionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VendorBookingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorServiceController;
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
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationsController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::patch('/notifications/{notification}/read', [NotificationsController::class, 'read'])->name('notifications.read');
    Route::get('/notifications/{notification}/read', [NotificationsController::class, 'read'])->name('notifications.read.get');
    Route::patch('/notifications/read-all', [NotificationsController::class, 'readAll'])->name('notifications.read-all');
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
    Route::post('/bookings/{booking:booking_code}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking:booking_code}/reviews', [ReviewController::class, 'store'])->name('bookings.reviews.store');
    Route::get('/bookings/{booking:booking_code}/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bookings/{booking:booking_code}/payments', [PaymentController::class, 'store'])->name('payments.store');
});

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'vendor'])->name('dashboard');
    Route::patch('/bookings/{booking:booking_code}/status', [VendorBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/bookings/{booking:booking_code}/staff', [StaffController::class, 'assign'])->name('bookings.staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::patch('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::patch('/staff/{staff}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');
    Route::post('/services', [VendorServiceController::class, 'store'])->name('services.store');
    Route::patch('/services/{service}', [VendorServiceController::class, 'update'])->name('services.update');
    Route::patch('/services/{service}/toggle', [VendorServiceController::class, 'toggle'])->name('services.toggle');
    Route::patch('/profile/{vendor}', [ProfileController::class, 'updateVendor'])->name('profile.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
    Route::patch('/vendors/{vendor}/verification', [AdminController::class, 'verifyVendor'])->name('vendors.verification');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');
    Route::patch('/services/{service}/status', [AdminController::class, 'updateServiceStatus'])->name('services.status');
    Route::patch('/bookings/{booking:booking_code}/status', [AdminController::class, 'updateBookingStatus'])->name('bookings.status');
    Route::get('/reports/pdf', [AdminController::class, 'report'])->name('reports.pdf');
});

Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/', [DashboardController::class, 'superAdmin'])->name('dashboard');
    Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
    Route::patch('/admins/{user}/toggle', [SuperAdminController::class, 'toggleAdmin'])->name('admins.toggle');
    Route::post('/categories', [SuperAdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/barangays', [SuperAdminController::class, 'storeBarangay'])->name('barangays.store');
});
