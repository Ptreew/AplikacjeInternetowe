<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\TicketController;

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

// Ticket routes - require authentication except search
Route::get('/tickets/search', [TicketController::class, 'create'])->name('tickets.search');
Route::post('/tickets/search', [TicketController::class, 'search'])->name('tickets.search.results');

Route::middleware(['auth'])->group(function () {
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
});
