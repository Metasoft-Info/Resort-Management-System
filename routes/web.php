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
use App\Http\Controllers\Admin\ExtraChargeCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\NotificationController;

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/convention-hall', [HomeController::class, 'conventionHall'])->name('convention-hall');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Auth routes - Admin Login is the main entry point
Route::get('/admin', [AuthController::class, 'showLogin'])->name('login');
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/profile', [AuthController::class, 'profile'])->name('admin.profile');
Route::post('/admin/profile', [AuthController::class, 'updateProfile'])->name('admin.profile.update');

// Admin routes (protected by auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/toggle-mode', [DashboardController::class, 'toggleMode'])->name('dashboard.toggle-mode');
    Route::get('/dashboard/search-rooms', [DashboardController::class, 'searchRoomAvailability']);
    Route::get('/dashboard/search-halls', [DashboardController::class, 'searchHallAvailability']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    
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
    Route::post('/bookings/{booking}/process-refund', [BookingController::class, 'processRefund'])->name('bookings.process-refund');
    Route::post('/bookings/{booking}/update-vat', [BookingController::class, 'updateVat'])->name('bookings.update-vat');
    
    // Premium Booking (Advanced Room Booking)
    Route::get('/premium-booking', [PremiumBookingController::class, 'index'])->name('premium-booking.index');
    Route::post('/premium-booking/search', [PremiumBookingController::class, 'search'])->name('premium-booking.search');
    Route::post('/premium-booking/book', [PremiumBookingController::class, 'book'])->name('premium-booking.book');
    Route::get('/premium-booking/search-customer', [PremiumBookingController::class, 'searchCustomer'])->name('premium-booking.search-customer');
    
    // Convention Halls
    Route::resource('convention-halls', ConventionHallController::class);
    
    // Convention Bookings - Custom routes BEFORE resource
    Route::get('/convention-bookings/customer/{phone}', [ConventionBookingController::class, 'searchCustomer'])->name('convention-bookings.search-customer');
    Route::get('/convention-bookings/find-by-phone', [ConventionBookingController::class, 'findByPhone'])->name('convention-bookings.find-by-phone');
    Route::post('/convention-bookings/check-availability', [ConventionBookingController::class, 'checkAvailability'])->name('convention-bookings.check-availability');
    Route::get('/convention-bookings/available-halls', [ConventionBookingController::class, 'getAvailableHalls'])->name('convention-bookings.available-halls');
    // Resource route AFTER custom routes
    Route::resource('convention-bookings', ConventionBookingController::class);
    Route::post('/convention-bookings/{conventionBooking}/add-payment', [ConventionBookingController::class, 'addPayment'])->name('convention-bookings.add-payment');
    Route::post('/convention-bookings/{conventionBooking}/update-status', [ConventionBookingController::class, 'updateStatus'])->name('convention-bookings.update-status');
    Route::post('/convention-bookings/{conventionBooking}/update-addons', [ConventionBookingController::class, 'updateAddons'])->name('convention-bookings.update-addons');
    
    // Premium Convention (Advanced Hall Booking)
    Route::get('/premium-convention', [PremiumConventionController::class, 'index'])->name('premium-convention.index');
    Route::post('/premium-convention/search', [PremiumConventionController::class, 'search'])->name('premium-convention.search');
    Route::post('/premium-convention/book', [PremiumConventionController::class, 'book'])->name('premium-convention.book');
    
    // Addon Services
    Route::resource('addon-services', AddonServiceController::class);
    
    // Extra Charge Categories
    Route::resource('extra-charge-categories', ExtraChargeCategoryController::class);
    Route::get('api/extra-charge-categories', [ExtraChargeCategoryController::class, 'getCategories'])->name('extra-charge-categories.api');
    
    // Food Packages
    Route::resource('food-packages', FoodPackageController::class);
    
    // Hero Slides
    Route::resource('hero-slides', HeroSlideController::class);
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/resort-info', [SettingsController::class, 'updateResortInfo'])->name('settings.resort-info');
    Route::post('/settings/navbar-links', [SettingsController::class, 'storeNavbarLink'])->name('settings.navbar-links.store');
    Route::put('/settings/navbar-links/{navbarLink}', [SettingsController::class, 'updateNavbarLink'])->name('settings.navbar-links.update');
    Route::delete('/settings/navbar-links/{navbarLink}', [SettingsController::class, 'destroyNavbarLink'])->name('settings.navbar-links.destroy');
    Route::post('/settings/footer-sections', [SettingsController::class, 'storeFooterSection'])->name('settings.footer-sections.store');
    Route::put('/settings/footer-sections/{footerSection}', [SettingsController::class, 'updateFooterSection'])->name('settings.footer-sections.update');
    Route::delete('/settings/footer-sections/{footerSection}', [SettingsController::class, 'destroyFooterSection'])->name('settings.footer-sections.destroy');
    Route::post('/settings/footer-links', [SettingsController::class, 'storeFooterLink'])->name('settings.footer-links.store');
    Route::put('/settings/footer-links/{footerLink}', [SettingsController::class, 'updateFooterLink'])->name('settings.footer-links.update');
    Route::delete('/settings/footer-links/{footerLink}', [SettingsController::class, 'destroyFooterLink'])->name('settings.footer-links.destroy');
    Route::post('/settings/logos', [SettingsController::class, 'updateLogos'])->name('settings.logos.update');
    Route::delete('/settings/logos/{type}', [SettingsController::class, 'deleteLogo'])->name('settings.logos.delete');
    Route::post('/settings/menus', [SettingsController::class, 'updateMenuSettings'])->name('settings.menus.update');
    Route::post('/settings/menus/seed', [SettingsController::class, 'seedMenus'])->name('settings.menus.seed');
    
    // Data Reset Routes
    Route::post('/settings/reset/room-bookings', [SettingsController::class, 'resetRoomBookings'])->name('settings.reset.room-bookings');
    Route::post('/settings/reset/convention-bookings', [SettingsController::class, 'resetConventionBookings'])->name('settings.reset.convention-bookings');
    Route::post('/settings/reset/all-bookings', [SettingsController::class, 'resetAllBookings'])->name('settings.reset.all-bookings');
    Route::post('/settings/clear/activity-logs', [SettingsController::class, 'clearActivityLogs'])->name('settings.clear.activity-logs');
    
    // Users
    Route::resource('users', UserController::class);
    
    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Today's Summary
    Route::get('/todays-summary', [TodaysSummaryController::class, 'index'])->name('todays-summary');
    
    // Reports
    Route::get('/reports/room-bookings', [ReportController::class, 'roomBookings'])->name('reports.room-bookings');
    Route::get('/reports/room-bookings/export', [ReportController::class, 'exportRoomBookings'])->name('reports.room-bookings.export');
    Route::get('/reports/advance-bookings', [ReportController::class, 'advanceBookings'])->name('reports.advance-bookings');
    Route::get('/reports/advance-bookings/export', [ReportController::class, 'exportAdvanceBookings'])->name('reports.advance-bookings.export');
    Route::get('/reports/unpaid-checked-in', [ReportController::class, 'unpaidCheckedIn'])->name('reports.unpaid-checked-in');
    Route::get('/reports/unpaid-checked-in/export', [ReportController::class, 'exportUnpaidCheckedIn'])->name('reports.unpaid-checked-in.export');
    Route::get('/reports/combined', [ReportController::class, 'combined'])->name('reports.combined');
    Route::get('/reports/combined/export', [ReportController::class, 'exportCombined'])->name('reports.combined.export');
    Route::get('/reports/convention-bookings', [ReportController::class, 'conventionBookings'])->name('reports.convention-bookings');
    Route::get('/reports/convention-bookings/export', [ReportController::class, 'exportConventionBookings'])->name('reports.convention-bookings.export');
    Route::get('/reports/police-station', [ReportController::class, 'policeStation'])->name('reports.police-station');
    Route::get('/reports/guest-extra-charges', [ReportController::class, 'guestExtraCharges'])->name('reports.guest-extra-charges');
    
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/{phone}', [CustomerController::class, 'show'])->name('customers.show');
});

