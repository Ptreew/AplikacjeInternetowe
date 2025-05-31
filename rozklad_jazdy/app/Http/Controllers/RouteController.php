<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Line;
use App\Models\Stop;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for create, store, edit, update, destroy
        $this->middleware('auth')->except(['index', 'show', 'search', 'searchResults', 'searchCityResults']);
        
        // Require admin role for create, store, edit, update, destroy
        $this->middleware('role:admin')->except(['index', 'show', 'search', 'searchResults', 'searchCityResults']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = Route::with(['line.carrier']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($request->has('line_id')) {
            $query->where('line_id', $request->line_id);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active == 1);
        }
        
        // Paginate the results
        $routes = $query->orderBy('line_id')->orderBy('name')->paginate(15);
        
        // Get lines for filter dropdown
        $lines = Line::with('carrier')->orderBy('name')->get();
        
        return view('routes.index', compact('routes', 'lines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lines = Line::with('carrier')->orderBy('name')->get();
        return view('routes.create', compact('lines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => 'required|exists:lines,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes')->where(function ($query) use ($request) {
                    return $query->where('line_id', $request->line_id);
                }),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Create new route
        $route = new Route();
        $route->line_id = $validated['line_id'];
        $route->name = $validated['name'];
        $route->is_active = $request->has('is_active');
        $route->save();
        
        return redirect()->route('routes.show', $route)
            ->with('success', 'Route created successfully. Now you can add stops to this route.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        // Load related data
        $route->load([
            'line.carrier',
            'routeStops' => function($query) {
                $query->orderBy('stop_number');
            },
            'routeStops.stop.city',
            'schedules.departures.vehicle',
            'schedules' => function($query) {
                $query->orderBy('day_type');
            }
        ]);
        
        return view('routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        $lines = Line::with('carrier')->orderBy('name')->get();
        return view('routes.edit', compact('route', 'lines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => 'required|exists:lines,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes')->where(function ($query) use ($request) {
                    return $query->where('line_id', $request->line_id);
                })->ignore($route->id),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Update route
        $route->line_id = $validated['line_id'];
        $route->name = $validated['name'];
        $route->is_active = $request->has('is_active');
        $route->save();
        
        return redirect()->route('routes.show', $route)
            ->with('success', 'Route updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        // Check if the route has any schedules
        if ($route->schedules()->exists()) {
            return redirect()->route('routes.show', $route)
                ->with('error', 'Cannot delete route with associated schedules. Please delete the schedules first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete associated route stops
            $route->routeStops()->delete();
            
            // Delete the route
            $route->delete();
            
            DB::commit();
            
            return redirect()->route('routes.index')
                ->with('success', 'Route deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('routes.show', $route)
                ->with('error', 'Failed to delete route: ' . $e->getMessage());
        }
    }
    
    /**
     * Search form for routes
     */
    public function search()
    {
        $cities = City::orderBy('name')->get();
        
        // Get all stops for the city search form
        $stops = Stop::orderBy('name')->get();
        
        // Get unique vehicle types for the dropdown menu
        $vehicleTypes = DB::table('vehicles')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        
        // Set active tab for the form
        $activeTab = 'miedzymiastowe';
        
        return view('index', compact('cities', 'stops', 'vehicleTypes', 'activeTab'));
    }
    
    /**
     * Search results for routes
     */
    public function searchResults(Request $request)
    {
        // Validate search parameters
        $validated = $request->validate([
            'from_city' => 'required|exists:cities,id',
            'to_city' => 'required|exists:cities,id|different:from_city',
            'date' => 'nullable|date',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i',
            'transport_type' => 'nullable|string'
        ], [
            'to_city.different' => 'Miasto początkowe i docelowe nie mogą być takie same. Wybierz różne miasta.'
        ]);
        
        // Find routes that connect the two cities with correct order (from_city must come before to_city)
        $routesQuery = Route::where('is_active', true)
            ->whereHas('routeStops', function($query) use ($request) {
                $query->whereHas('stop', function($q) use ($request) {
                    $q->where('city_id', $request->from_city);
                });
                // Store the stop_number for the 'from' city to use in the next query
                $query->addSelect('stop_number');
            })
            ->whereHas('routeStops', function($query) use ($request) {
                $query->whereHas('stop', function($q) use ($request) {
                    $q->where('city_id', $request->to_city);
                });
                // Ensure that the 'to' city stop comes after the 'from' city stop
                $query->whereRaw('stop_number > (SELECT rs.stop_number FROM route_stops rs JOIN stops s ON rs.stop_id = s.id WHERE rs.route_id = route_stops.route_id AND s.city_id = ?)', [$request->from_city]);
            })
            ->with([
                'line.carrier',
                'routeStops' => function($query) {
                    $query->orderBy('stop_number');
                },
                'routeStops.stop.city',
                'schedules' => function($query) use ($request) {
                    // If date is provided, map to the appropriate day_type
                    if ($request->filled('date')) {
                        $dayOfWeek = date('w', strtotime($request->date));
                        
                        // Convert numeric day of week to day_type enum value
                        // 0 = Sunday, 6 = Saturday, 1-5 = weekdays
                        $dayType = 'weekday';
                        if ($dayOfWeek == 0) {
                            $dayType = 'sunday';
                        } elseif ($dayOfWeek == 6) {
                            $dayType = 'saturday';
                        }
                        
                        $query->where('day_type', $dayType);
                    }
                    
                    // Filter by valid date range
                    if ($request->filled('date')) {
                        $query->where('valid_from', '<=', $request->date)
                              ->where('valid_to', '>=', $request->date);
                    }
                },
                'schedules.departures' => function($query) use ($request) {
                    $query->orderBy('departure_time');
                    
                    // Time range filtering
                    if ($request->filled('time_from')) {
                        $query->whereTime('departure_time', '>=', $request->time_from);
                    }
                    
                    if ($request->filled('time_to')) {
                        $query->whereTime('departure_time', '<=', $request->time_to);
                    }
                    
                    // Transport type filtering
                    if ($request->filled('transport_type')) {
                        $query->whereHas('vehicle', function($q) use ($request) {
                            $q->where('type', $request->transport_type);
                        });
                    }
                },
                'schedules.departures.vehicle'
            ]);
        
        $routes = $routesQuery->get();
        $fromCity = City::findOrFail($request->from_city);
        $toCity = City::findOrFail($request->to_city);
        
        // Get cities for the search form
        $cities = City::orderBy('name')->get();
        
        // Pobierz unikalne typy pojazdów do dropdown'u
        $vehicleTypes = DB::table('vehicles')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        
        // Filter out routes with no valid schedules or departures
        $routes = $routes->filter(function($route) {
            return $route->schedules->isNotEmpty() && 
                   $route->schedules->contains(function($schedule) {
                       return $schedule->departures->isNotEmpty();
                   });
        });
        
        // If after filtering there are no routes left
        if ($routes->isEmpty()) {
            $activeTab = $request->input('active_tab', 'miedzymiastowe');
            return back()->withInput()
                ->with('error', 'Nie znaleziono żadnych kursów dla wybranych miast i kryteriów czasowych.')
                ->with('activeTab', $activeTab);
        }
        
        // Paginate the filtered results (10 items per page)
        $currentPage = request()->has('page') ? request()->get('page') : 1;
        $perPage = 10;
        $routesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $routes->forPage($currentPage, $perPage),
            $routes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        // Set active tab for the form - use the value from the request or default to 'miedzymiastowe'
        $activeTab = $request->input('active_tab', 'miedzymiastowe');
        
        return view('index', compact('routesPaginator', 'fromCity', 'toCity', 'request', 'cities', 'vehicleTypes', 'activeTab'));
    }
    
    /**
     * Search results for city routes (local transportation)
     */
    public function searchCityResults(Request $request)
    {
        // Validate search parameters
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'from_stop' => 'required|exists:stops,id',
            'to_stop' => 'required|exists:stops,id|different:from_stop',
            'date' => 'nullable|date',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i',
            'transport_type' => 'nullable|string'
        ], [
            'to_stop.different' => 'Przystanek początkowy i docelowy nie mogą być takie same. Wybierz różne przystanki.'
        ]);
        
        // Find routes that connect the two stops
        $routesQuery = Route::where('is_active', true)
            ->whereHas('routeStops', function($query) use ($request) {
                $query->where('stop_id', $request->from_stop);
            })
            ->whereHas('routeStops', function($query) use ($request) {
                $query->where('stop_id', $request->to_stop);
            })
            ->with([
                'line.carrier',
                'routeStops' => function($query) {
                    $query->orderBy('stop_number');
                },
                'routeStops.stop',
                'schedules' => function($query) use ($request) {
                    // If date is provided, use it for filtering, otherwise use today's date
                    $dateToUse = $request->filled('date') ? $request->date : date('Y-m-d');
                    
                    // Filter by valid date range
                    $query->where('valid_from', '<=', $dateToUse)
                          ->where('valid_to', '>=', $dateToUse);
                          
                    // If date is provided, map to the appropriate day_type
                    if ($request->filled('date')) {
                        $dayOfWeek = date('w', strtotime($request->date));
                        
                        // Convert numeric day of week to day_type enum value
                        // 0 = Sunday, 6 = Saturday, 1-5 = weekdays
                        $dayType = 'weekday';
                        if ($dayOfWeek == 0) {
                            $dayType = 'sunday';
                        } elseif ($dayOfWeek == 6) {
                            $dayType = 'saturday';
                        }
                        
                        $query->where('day_type', $dayType);
                    }
                },
                'schedules.departures' => function($query) use ($request) {
                    $query->orderBy('departure_time');
                    
                    // Time range filtering
                    if ($request->filled('time_from')) {
                        $query->whereTime('departure_time', '>=', $request->time_from);
                    }
                    
                    if ($request->filled('time_to')) {
                        $query->whereTime('departure_time', '<=', $request->time_to);
                    }
                    
                    // Transport type filtering
                    if ($request->filled('transport_type')) {
                        $query->whereHas('vehicle', function($q) use ($request) {
                            $q->where('type', $request->transport_type);
                        });
                    }
                },
                'schedules.departures.vehicle'
            ]);
        
        // Execute the query
        $routes = $routesQuery->get();
        
        // Get the stop information for display
        $fromStop = Stop::with('city')->find($request->from_stop);
        $toStop = Stop::with('city')->find($request->to_stop);
        
        // Check if any routes were found
        if ($routes->isEmpty()) {
            return back()->withInput()->with('error', 'Nie znaleziono żadnych połączeń dla wybranych przystanków.');
        }
        
        // Filter routes to ensure proper stop order (from_stop comes before to_stop)
        $routes = $routes->filter(function($route) use ($request) {
            $fromStopPosition = null;
            $toStopPosition = null;
            
            foreach ($route->routeStops as $routeStop) {
                if ($routeStop->stop_id == $request->from_stop) {
                    $fromStopPosition = $routeStop->stop_number;
                }
                if ($routeStop->stop_id == $request->to_stop) {
                    $toStopPosition = $routeStop->stop_number;
                }
            }
            
            // Only keep routes where from_stop comes before to_stop
            return $fromStopPosition !== null && $toStopPosition !== null && $fromStopPosition < $toStopPosition;
        });
        
        // Filter out routes with no valid schedules or departures
        $routes = $routes->filter(function($route) {
            return $route->schedules->isNotEmpty() && 
                   $route->schedules->contains(function($schedule) {
                       return $schedule->departures->isNotEmpty();
                   });
        });
        
        // If after filtering there are no routes left
        if ($routes->isEmpty()) {
            $activeTab = $request->input('active_tab', 'miejskie');
            return back()->withInput()
                ->with('error', 'Nie znaleziono żadnych połączeń dla wybranych przystanków.')
                ->with('activeTab', $activeTab);
        }
        
        // Get data for the search form
        $cities = City::orderBy('name')->get();
        $stops = Stop::orderBy('name')->get();
        
        // Get unique vehicle types for the dropdown menu
        $vehicleTypes = DB::table('vehicles')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        
        // Paginate the filtered results (10 items per page)
        $currentPage = request()->has('page') ? request()->get('page') : 1;
        $perPage = 10;
        $routesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $routes->forPage($currentPage, $perPage),
            $routes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        // Set active tab for the form - use the value from the request or default to 'miejskie'
        $activeTab = $request->input('active_tab', 'miejskie');
        
        return view('index', compact('routesPaginator', 'fromStop', 'toStop', 'request', 'cities', 'stops', 'vehicleTypes', 'activeTab'));
    }
}
