<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PilotTripController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED_LOCALES, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back(fallback: auth()->check() ? route('dashboard') : route('login'));
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'role:staff,admin,pilot'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role-aware main dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Live Availability Calendar (Vehicles & 4 HQ Pilots)
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/api/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Bookings (Staff & Admin submit/list; Pilot can view assigned booking & log odometer)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::middleware('role:staff,admin')->group(function () {
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // Trip Completion & Odometer Sync (Pilot & Admin)
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])
        ->middleware('role:pilot,admin')
        ->name('bookings.complete');

    // Official YKN PDF Generation
    Route::get('/bookings/{booking}/pdf/borang', [PdfController::class, 'borangKenderaan'])
        ->name('bookings.pdf.borang');
    Route::get('/bookings/{booking}/pdf/memo', [PdfController::class, 'memoMemandu'])
        ->name('bookings.pdf.memo');

    // Pilot Mobile Portal
    Route::middleware('role:pilot')->prefix('pilot')->name('pilot.')->group(function () {
        Route::get('/trips', [PilotTripController::class, 'index'])->name('trips.index');
    });

    // Unit Pentadbiran (Admin) Management Routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('vehicles', VehicleController::class)->except(['show']);
            Route::resource('users', UserController::class)->except(['show', 'destroy']);
            Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
                ->name('users.toggle-active');
        });
    });
});
