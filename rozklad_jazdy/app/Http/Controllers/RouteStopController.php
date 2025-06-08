<?php

namespace App\Http\Controllers;

use App\Models\RouteStop;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Middleware\CheckRole;

class RouteStopController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods
        $this->middleware('auth');
        
        // Require admin role for all methods
        $this->middleware(CheckRole::class . ':admin');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = RouteStop::with(['route.line', 'stop.city']);
        
        if ($request->has('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        
        if ($request->has('stop_id')) {
            $query->where('stop_id', $request->stop_id);
        }
        
        // Paginate the results
        $routeStops = $query->orderBy('route_id')->orderBy('stop_number')->paginate(15);
        
        // Get routes and stops for filter dropdowns
        $routes = Route::with('line')->orderBy('name')->get();
        $stops = Stop::with('city')->orderBy('name')->get();
        
        return view('route_stops.index', compact('routeStops', 'routes', 'stops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $routes = Route::with('line')->orderBy('name')->get();
        $stops = Stop::with('city')->where('is_active', true)->orderBy('name')->get();
        
        // Pre-select route if provided in the request
        $selectedRouteId = $request->route_id;
        
        // If a route is selected, get the current route stops to show the sequence
        $currentRouteStops = null;
        if ($selectedRouteId) {
            $currentRouteStops = RouteStop::where('route_id', $selectedRouteId)
                ->orderBy('stop_number')
                ->with('stop.city')
                ->get();
        }
        
        return view('route_stops.create', compact('routes', 'stops', 'selectedRouteId', 'currentRouteStops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'stop_id' => [
                'required',
                'exists:stops,id',
                Rule::unique('route_stops')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                }),
            ],
            'stop_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('route_stops')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                }),
            ],
            'distance_from_start' => 'required|integer|min:0',
            'time_to_next' => 'nullable|integer|min:0',
        ]);
        
        // Create new route stop
        $routeStop = new RouteStop();
        $routeStop->route_id = $validated['route_id'];
        $routeStop->stop_id = $validated['stop_id'];
        $routeStop->stop_number = $validated['stop_number'];
        $routeStop->distance_from_start = $validated['distance_from_start'];
        $routeStop->time_to_next = $validated['time_to_next'];
        $routeStop->save();
        
        return redirect()->route('routes.show', $routeStop->route_id)
            ->with('success', 'Stop added to route successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RouteStop $routeStop)
    {
        // Load related data
        $routeStop->load(['route.line', 'stop.city']);
        
        return view('route_stops.show', compact('routeStop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RouteStop $routeStop)
    {
        $routes = Route::with('line')->orderBy('name')->get();
        $stops = Stop::with('city')->where('is_active', true)->orderBy('name')->get();
        
        // Get the current route stops to show the sequence
        $currentRouteStops = RouteStop::where('route_id', $routeStop->route_id)
            ->orderBy('stop_number')
            ->with('stop.city')
            ->get();
            
        return view('route_stops.edit', compact('routeStop', 'routes', 'stops', 'currentRouteStops'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RouteStop $routeStop)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'stop_id' => [
                'required',
                'exists:stops,id',
                Rule::unique('route_stops')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                })->ignore($routeStop->id),
            ],
            'stop_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('route_stops')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                })->ignore($routeStop->id),
            ],
            'distance_from_start' => 'required|integer|min:0',
            'time_to_next' => 'nullable|integer|min:0',
        ]);
        
        // Update route stop
        $routeStop->route_id = $validated['route_id'];
        $routeStop->stop_id = $validated['stop_id'];
        $routeStop->stop_number = $validated['stop_number'];
        $routeStop->distance_from_start = $validated['distance_from_start'];
        $routeStop->time_to_next = $validated['time_to_next'];
        $routeStop->save();
        
        return redirect()->route('routes.show', $routeStop->route_id)
            ->with('success', 'Route stop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RouteStop $routeStop)
    {
        $routeId = $routeStop->route_id;
        
        try {
            DB::beginTransaction();
            
            // Delete the route stop
            $routeStop->delete();
            
            // Reorder stop numbers
            $remainingStops = RouteStop::where('route_id', $routeId)
                ->orderBy('stop_number')
                ->get();
                
            $counter = 1;
            foreach ($remainingStops as $stop) {
                $stop->stop_number = $counter++;
                $stop->save();
            }
            
            DB::commit();
            
            return redirect()->route('routes.show', $routeId)
                ->with('success', 'Stop removed from route successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('routes.show', $routeId)
                ->with('error', 'Failed to remove stop from route: ' . $e->getMessage());
        }
    }
}
