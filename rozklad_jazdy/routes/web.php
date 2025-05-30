<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartureController;
use App\Http\Controllers\FavouriteLineController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteStopController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VehicleController;

// Main pages
Route::get('/', function () {
    return view('index');
})->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Public search routes
Route::get('/search', [RouteController::class, 'search'])->name('routes.search');
Route::post('/search', [RouteController::class, 'searchResults'])->name('routes.search.results');

// Carrier routes (public listing)
Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
Route::get('/carriers/{carrier}', [CarrierController::class, 'show'])->name('carriers.show');

// Line routes (public listing)
Route::get('/lines', [LineController::class, 'index'])->name('lines.index');
Route::get('/lines/{line}', [LineController::class, 'show'])->name('lines.show');

// Route routes (public listing)
Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
Route::get('/routes/{route}', [RouteController::class, 'show'])->name('routes.show');

// City routes (public listing)
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
Route::get('/cities/{city}', [CityController::class, 'show'])->name('cities.show');

// Ticket routes - require authentication except search
Route::get('/tickets/search', [TicketController::class, 'create'])->name('tickets.search');
Route::post('/tickets/search', [TicketController::class, 'search'])->name('tickets.search.results');

// User authenticated routes
Route::middleware(['auth'])->group(function () {
    // User favorite lines
    Route::get('/favorites', [LineController::class, 'favorites'])->name('lines.favorites');
    Route::post('/lines/{line}/favorite', [LineController::class, 'toggleFavorite'])->name('lines.toggleFavorite');
    
    // Standard resource routes for tickets
    Route::resource('tickets', TicketController::class);
    
    // Additional custom routes for ticket management
    Route::post('/tickets/{ticket}/pay', [TicketController::class, 'pay'])->name('tickets.pay');
});


// Secure Admin routes with explicit middleware class
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Admin panel management view
    Route::get('/', function() {
        return view('admin.index');
    })->name('admin');
    
    // Admin form submission routes
    Route::post('/miedzymiastowe', function () {
        return redirect()->back()->with('success', 'Kurs został dodany');
    })->name('admin.miedzymiastowe.store');

    Route::post('/miejskie', function () {
        return redirect()->back()->with('success', 'Kurs został dodany');
    })->name('admin.miejskie.store');

    Route::post('/users', function () {
        return redirect()->back()->with('success', 'Użytkownik został dodany');
    })->name('admin.users.store');

    Route::post('/vehicles', function () {
        return redirect()->back()->with('success', 'Pojazd został dodany');
    })->name('admin.vehicles.store');

    Route::post('/carriers', function () {
        return redirect()->back()->with('success', 'Przewoźnik został dodany');
    })->name('admin.carriers.store');
    
        // Admin ticket management
    Route::post('/tickets/{ticket}/mark-as-used', [TicketController::class, 'markAsUsed'])->name('admin.tickets.mark-as-used');
    
    // Admin resource routes
    Route::resource('carriers', CarrierController::class)->except(['index', 'show']);
    Route::resource('lines', LineController::class)->except(['index', 'show']);
    Route::resource('routes', RouteController::class)->except(['index', 'show']);
    Route::resource('cities', CityController::class)->except(['index', 'show']);
    Route::resource('stops', StopController::class);
    Route::resource('route-stops', RouteStopController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('departures', DepartureController::class);
    Route::resource('vehicles', VehicleController::class);
});
