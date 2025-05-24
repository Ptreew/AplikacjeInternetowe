<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Main pages
Route::get('/', function () {
    return view('index');
})->name('home');

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Admin routes
Route::get('/admin', function () {
    return view('admin.index');
})->name('admin');

// Admin form submission routes
Route::post('/admin/miedzymiastowe', function () {
    // Controller logic would go here in a real app
    return redirect()->back()->with('success', 'Kurs został dodany');
})->name('admin.miedzymiastowe.store');

Route::post('/admin/miejskie', function () {
    // Controller logic would go here in a real app
    return redirect()->back()->with('success', 'Kurs został dodany');
})->name('admin.miejskie.store');

Route::post('/admin/users', function () {
    // Controller logic would go here in a real app
    return redirect()->back()->with('success', 'Użytkownik został dodany');
})->name('admin.users.store');

Route::post('/admin/vehicles', function () {
    // Controller logic would go here in a real app
    return redirect()->back()->with('success', 'Pojazd został dodany');
})->name('admin.vehicles.store');

Route::post('/admin/carriers', function () {
    // Controller logic would go here in a real app
    return redirect()->back()->with('success', 'Przewoźnik został dodany');
})->name('admin.carriers.store');

// Authentication form handling - in a real app these would use Laravel's authentication system
Route::post('/login', function () {
    // Login logic would go here
    return redirect()->route('home');
})->name('login.post');

Route::post('/register', function () {
    // Registration logic would go here
    return redirect()->route('login');
})->name('register.post');
