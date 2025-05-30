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
        $this->middleware('auth')->except(['index', 'show', 'search', 'searchResults']);
        
        // Require admin role for create, store, edit, update, destroy
        $this->middleware('role:admin')->except(['index', 'show', 'search', 'searchResults']);
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
            'schedules'
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
        return view('routes.search', compact('cities'));
    }
    
    /**
     * Search results for routes
     */
    public function searchResults(Request $request)
    {
        $request->validate([
            'from_city' => 'required|exists:cities,id',
            'to_city' => 'required|exists:cities,id|different:from_city',
            'date' => 'nullable|date',
        ]);
        
        // Find routes that connect the two cities
        $routes = Route::where('is_active', true)
            ->whereHas('routeStops.stop', function($query) use ($request) {
                $query->where('city_id', $request->from_city);
            })
            ->whereHas('routeStops.stop', function($query) use ($request) {
                $query->where('city_id', $request->to_city);
            })
            ->with([
                'line.carrier',
                'routeStops' => function($query) {
                    $query->orderBy('stop_number');
                },
                'routeStops.stop.city'
            ])
            ->get();
            
        // Get the city names for display
        $fromCity = City::find($request->from_city);
        $toCity = City::find($request->to_city);
        
        return view('routes.search_results', compact('routes', 'fromCity', 'toCity', 'request'));
    }
}
