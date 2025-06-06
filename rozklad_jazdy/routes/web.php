<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartureController;
use App\Http\Controllers\FavouriteLineController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteStopController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Admin\AdminCityController;
use App\Http\Controllers\RouteBuilderController;

// Main pages
Route::get('/', [HomeController::class, 'index'])->name('home');

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
Route::post('/search/city', [RouteController::class, 'searchCityResults'])->name('routes.search.city');
Route::get('/stops/by-city/{city}', [StopController::class, 'getStopsByCity'])->name('stops.by.city');

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

// Ticket creation page available publicly (will redirect to login inside controller if not authenticated)
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');

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
    Route::post('/tickets/{ticket}/mark-used', [TicketController::class, 'markAsUsed'])->name('tickets.mark-used');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});


// Secure Admin routes with explicit middleware class
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Route Builder - multi-step route creation wizard
    Route::prefix('routes/builder')->name('admin.routes.builder.')->group(function() {
        // Redirect from base path to step1
        Route::get('/', function() {
            return redirect()->route('admin.routes.builder.step1');
        })->name('index');
        
        // Step 1: Basic route info
        Route::get('/step1', [RouteBuilderController::class, 'showStep1'])->name('step1');
        Route::post('/step1', [RouteBuilderController::class, 'processStep1'])->name('step1.process');
        
        // Step 2: Stops management
        Route::get('/step2', [RouteBuilderController::class, 'showStep2'])->name('step2');
        Route::post('/step2', [RouteBuilderController::class, 'processStep2'])->name('step2.process');
        Route::get('/stops-by-city/{city}', [RouteBuilderController::class, 'getStopsByCity'])->name('stops-by-city');
        
        // Step 3: Schedules and departures
        Route::get('/step3', [RouteBuilderController::class, 'showStep3'])->name('step3');
        Route::post('/step3', [RouteBuilderController::class, 'processStep3'])->name('step3.process');
        
        // Cancel route creation
        Route::get('/cancel', [RouteBuilderController::class, 'cancel'])->name('cancel');
    });
    
    // City Routes Management
    Route::prefix('city-routes')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'index'])->name('admin.city_routes.index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'create'])->name('admin.city_routes.create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'store'])->name('admin.city_routes.store');
        Route::get('/{route}/edit', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'edit'])->name('admin.city_routes.edit');
        Route::put('/{route}', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'update'])->name('admin.city_routes.update');
        Route::delete('/{route}', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'destroy'])->name('admin.city_routes.destroy');
        Route::get('/get-stops', [\App\Http\Controllers\Admin\AdminCityRouteController::class, 'getStopsForCity'])->name('admin.city_routes.get_stops');
    });
    
    // Admin panel management view
    Route::get('/', function() {
        // Fetch the latest cities and stops to display in the panel
        $cities = \App\Models\City::orderBy('id', 'desc')->take(10)->get();
        $stops = \App\Models\Stop::with('city')->orderBy('id', 'desc')->take(10)->get();
        
        return view('admin.index', compact('cities', 'stops'));
    })->name('admin');
    
    // Admin resource routes
    Route::resource('carriers', CarrierController::class)
        ->names('admin.carriers')
        ->except(['index', 'show', 'store']);
    Route::resource('lines', LineController::class)
        ->names('admin.lines')
        ->except(['index', 'show']);
    Route::resource('routes', \App\Http\Controllers\Admin\AdminRouteController::class)
        ->names('admin.routes')
        ->except(['create']);

    // Przekierowanie z create na builder
    Route::get('/routes/create', function() {
        return redirect()->route('admin.routes.builder.step1');
    })->name('admin.routes.create');
    Route::resource('cities', AdminCityController::class)
        ->names('admin.cities');
    Route::resource('stops', \App\Http\Controllers\Admin\AdminStopController::class)
        ->names('admin.stops');
    
    // Intercity routes management
    Route::resource('intercity', \App\Http\Controllers\Admin\AdminIntercityController::class)
        ->names('admin.intercity');
        
    // Route stops management
    Route::resource('route_stops', \App\Http\Controllers\Admin\AdminRouteStopController::class)
        ->names('admin.route_stops')
        ->except(['index', 'create', 'show', 'edit']);
    Route::get('intercity/stops-for-city', [\App\Http\Controllers\Admin\AdminIntercityController::class, 'getStopsForCity'])
        ->name('admin.intercity.stops-for-city');
        
    Route::resource('route-stops', RouteStopController::class)
        ->names('admin.route-stops');
    Route::resource('schedules', ScheduleController::class)
        ->names('admin.schedules');
    Route::resource('departures', DepartureController::class)
        ->names('admin.departures');
    Route::resource('vehicles', VehicleController::class)
        ->names('admin.vehicles')
        ->except('store');
    Route::resource('tickets', \App\Http\Controllers\Admin\AdminTicketController::class)
        ->names('admin.tickets');
    
    // Admin form submission routes
    Route::post('/miedzymiastowe', function () {
        return redirect()->back()->with('success', 'Kurs został dodany');
    })->name('admin.miedzymiastowe.store');

    Route::post('/miejskie', function () {
        return redirect()->back()->with('success', 'Kurs został dodany');
    })->name('admin.miejskie.store');

    // User management routes
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/users/{user}/update-role', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateRole'])->name('admin.users.update-role');

    // Vehicle management routes
    Route::get('/vehicles', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'index'])->name('admin.vehicles.index');
    Route::get('/vehicles/create', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'create'])->name('admin.vehicles.create');
    Route::post('/vehicles', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'store'])->name('admin.vehicles.store');
    Route::get('/vehicles/{vehicle}', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'show'])->name('admin.vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'edit'])->name('admin.vehicles.edit');
    Route::put('/vehicles/{vehicle}', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'update'])->name('admin.vehicles.update');
    Route::delete('/vehicles/{vehicle}', [\App\Http\Controllers\Admin\AdminVehicleController::class, 'destroy'])->name('admin.vehicles.destroy');

    // Carrier management routes
    Route::get('/carriers', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'index'])->name('admin.carriers.index');
    Route::get('/carriers/create', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'create'])->name('admin.carriers.create');
    Route::post('/carriers', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'store'])->name('admin.carriers.store');
    Route::get('/carriers/{carrier}', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'show'])->name('admin.carriers.show');
    Route::get('/carriers/{carrier}/edit', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'edit'])->name('admin.carriers.edit');
    Route::put('/carriers/{carrier}', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'update'])->name('admin.carriers.update');
    Route::delete('/carriers/{carrier}', [\App\Http\Controllers\Admin\AdminCarrierController::class, 'destroy'])->name('admin.carriers.destroy');
    
    // Line management routes
    Route::get('/lines', [\App\Http\Controllers\Admin\AdminLineController::class, 'index'])->name('admin.lines.index');
    Route::get('/lines/create', [\App\Http\Controllers\Admin\AdminLineController::class, 'create'])->name('admin.lines.create');
    Route::post('/lines', [\App\Http\Controllers\Admin\AdminLineController::class, 'store'])->name('admin.lines.store');
    Route::get('/lines/{line}', [\App\Http\Controllers\Admin\AdminLineController::class, 'show'])->name('admin.lines.show');
    Route::get('/lines/{line}/edit', [\App\Http\Controllers\Admin\AdminLineController::class, 'edit'])->name('admin.lines.edit');
    Route::put('/lines/{line}', [\App\Http\Controllers\Admin\AdminLineController::class, 'update'])->name('admin.lines.update');
    Route::delete('/lines/{line}', [\App\Http\Controllers\Admin\AdminLineController::class, 'destroy'])->name('admin.lines.destroy');
    
    // Admin ticket management
    Route::post('/tickets/{ticket}/mark-as-used', [TicketController::class, 'markAsUsed'])->name('admin.tickets.mark-as-used');
    
    // Admin routes for cities
    Route::resource('cities', AdminCityController::class)->names([
        'index' => 'admin.cities.index',
        'create' => 'admin.cities.create',
        'store' => 'admin.cities.store',
        'show' => 'admin.cities.show',
        'edit' => 'admin.cities.edit',
        'update' => 'admin.cities.update',
        'destroy' => 'admin.cities.destroy',
    ]);
    

});
