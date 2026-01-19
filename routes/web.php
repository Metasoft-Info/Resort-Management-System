<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConventionHallController;
use App\Http\Controllers\ConventionBookingController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\AddonServiceController;
use App\Http\Controllers\Admin\FoodPackageController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TodaysSummaryController;
use App\Http\Controllers\Admin\PremiumBookingController;
use App\Http\Controllers\Admin\PremiumConventionController;

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/convention-hall', [HomeController::class, 'conventionHall'])->name('convention-hall');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Auth routes - Admin Login is the main entry point
Route::get('/admin', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin routes (protected by auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/search-rooms', [DashboardController::class, 'searchRoomAvailability']);
    Route::get('/dashboard/search-halls', [DashboardController::class, 'searchHallAvailability']);
    
    // Rooms
    Route::resource('rooms', RoomController::class);
    
    // Room Types
    Route::resource('room-types', RoomTypeController::class);
    
    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/update-status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{booking}/update-time', [BookingController::class, 'updateTime'])->name('bookings.update-time');
    Route::post('/bookings/{booking}/add-payment', [BookingController::class, 'addPayment'])->name('bookings.add-payment');
    Route::post('/bookings/{booking}/add-extra-charges', [BookingController::class, 'addExtraCharges'])->name('bookings.add-extra-charges');
    Route::post('/bookings/{booking}/add-guest', [BookingController::class, 'addGuest'])->name('bookings.add-guest');
    
    // Premium Booking (Advanced Room Booking)
    Route::get('/premium-booking', [PremiumBookingController::class, 'index'])->name('premium-booking.index');
    Route::post('/premium-booking/search', [PremiumBookingController::class, 'search'])->name('premium-booking.search');
    Route::post('/premium-booking/book', [PremiumBookingController::class, 'book'])->name('premium-booking.book');
    
    // Convention Halls
    Route::resource('convention-halls', ConventionHallController::class);
    
    // Convention Bookings
    Route::resource('convention-bookings', ConventionBookingController::class);
    
    // Premium Convention (Advanced Hall Booking)
    Route::get('/premium-convention', [PremiumConventionController::class, 'index'])->name('premium-convention.index');
    Route::post('/premium-convention/search', [PremiumConventionController::class, 'search'])->name('premium-convention.search');
    Route::post('/premium-convention/book', [PremiumConventionController::class, 'book'])->name('premium-convention.book');
    
    // Addon Services
    Route::resource('addon-services', AddonServiceController::class);
    
    // Food Packages
    Route::resource('food-packages', FoodPackageController::class);
    
    // Hero Slides
    Route::resource('hero-slides', HeroSlideController::class);
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Users
    Route::resource('users', UserController::class);
    
    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Today's Summary
    Route::get('/todays-summary', [TodaysSummaryController::class, 'index'])->name('todays-summary');
    
    // Reports
    Route::get('/reports/room-bookings', [ReportController::class, 'roomBookings'])->name('reports.room-bookings');
    Route::get('/reports/convention-bookings', [ReportController::class, 'conventionBookings'])->name('reports.convention-bookings');
});

