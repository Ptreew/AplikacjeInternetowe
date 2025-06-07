<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrier;
use App\Models\Line;
use App\Models\Route;
use App\Models\City;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Schedule;
use App\Models\Departure;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCityRouteController extends Controller
{
    /**
     * Display a listing of the city routes
     */
    public function index()
    {
        $routes = Route::with(['line.carrier', 'routeStops.stop.city'])
                  ->where('type', 'city')
                  ->orderBy('id', 'desc')
                  ->paginate(10);
                  
        return view('admin.city_routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new city route
     */
    public function create()
    {
        // Get all carriers
        $carriers = Carrier::all();
        
        // Get all cities
        $cities = City::all();
        
        // Get all vehicles with relation to line and carrier
        $vehicles = Vehicle::with('line.carrier')
            ->where('is_active', true)
            ->orderBy('vehicle_number')
            ->get();
            
        // Get all lines with relation to carrier
        $existingLines = Line::with('carrier')
            ->orderBy('carrier_id')
            ->orderBy('number')
            ->get();
            
        $daysOfWeek = [
            'monday' => 'Poniedziałek',
            'tuesday' => 'Wtorek',
            'wednesday' => 'Środa',
            'thursday' => 'Czwartek',
            'friday' => 'Piątek',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
        ];

        return view('admin.city_routes.create', compact('carriers', 'cities', 'vehicles', 'existingLines', 'daysOfWeek'));
    }

    /**
     * Store a newly created city route
     */
    public function store(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'line_id' => 'required|exists:lines,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // City for stops selection
            'line_number' => 'sometimes|string|max:10', // Added line number (optional)
            'travel_time' => 'required|integer|min:1',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'sometimes|boolean',
            'stops' => 'sometimes|array',
            'stops.*' => 'sometimes|exists:stops,id'
        ]);

        try {
            DB::beginTransaction();

            // Ensure the line belongs to the selected carrier
            $line = Line::where('id', $validatedData['line_id'])
                     ->where('carrier_id', $validatedData['carrier_id'])
                     ->firstOrFail();
            
            // Ensure the line has a number (required for city routes)
            if (empty($line->number)) {
                // If number is empty and line_number is provided, use it
                if (isset($validatedData['line_number']) && !empty($validatedData['line_number'])) {
                    $line->number = $validatedData['line_number'];
                } else {
                    // Otherwise use the default number (unique ID)
                    $line->number = 'M' . $line->id; // Prefix M for city routes
                }
                $line->save();
            }

            // Create new route
            $route = Route::create([
                'line_id' => $line->id,
                'type' => 'city',
                'name' => $validatedData['name'],
                'travel_time' => $validatedData['travel_time'],
                'is_active' => isset($validatedData['is_active']) ? $validatedData['is_active'] : true
            ]);
            
            // If stops are provided, create RouteStops for them
            if (isset($validatedData['stops']) && is_array($validatedData['stops']) && count($validatedData['stops']) > 0) {
                $stopNumber = 1;
                $distanceFromStart = 0;
                
                foreach ($validatedData['stops'] as $stopId) {
                    // Check if stop belongs to the selected city
                    $stop = Stop::where('id', $stopId)
                              ->where('city_id', $validatedData['city_id'])
                              ->firstOrFail();
                    
                    // Create route stop
                    RouteStop::create([
                        'route_id' => $route->id,
                        'stop_id' => $stopId,
                        'stop_number' => $stopNumber++,
                        'distance_from_start' => $distanceFromStart,
                        // Default time to next stop is 5 minutes
                        'time_to_next' => 5
                    ]);
                    
                    // Add 1 km to distance from start
                    $distanceFromStart += 1000;
                }  
            }

            // Create schedule
            $schedule = Schedule::create([
                'route_id' => $route->id,
                'valid_from' => now(),
                'valid_to' => now()->addYears(1), // Example validity period of 1 year
                'days_of_week' => $validatedData['days_of_week']
            ]);

            // Assign vehicle to line
            $vehicle = Vehicle::find($validatedData['vehicle_id']);
            $vehicle->line_id = $line->id;
            $vehicle->save();

            DB::commit();

            return redirect()->route('admin.city_routes.edit', $route->id)
                ->with('success', 'Trasa miejska została utworzona pomyślnie!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Wystąpił błąd podczas tworzenia trasy: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified city route
     */
    public function edit($id)
    {
        // Get route with all related data
        $route = Route::with(['line.carrier', 'routeStops.stop.city', 'schedules'])
                ->findOrFail($id);
        
        // Get all carriers
        $carriers = Carrier::orderBy('name')->get(['id', 'name']);
        
        // Get all cities
        $cities = City::orderBy('name')->get(['id', 'name', 'voivodeship']);
        
        // Get all vehicles
        $vehicles = Vehicle::with('line.carrier')
            ->where('is_active', true)
            ->orderBy('vehicle_number')
            ->get(['id', 'type', 'vehicle_number', 'capacity', 'line_id']);
            
        // Days of week for view
        $daysOfWeek = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '0' => 'Niedziela',
        ];
    
    // Get selected days of week from the first schedule
    $selectedDaysOfWeek = $route->schedules->first() ? $route->schedules->first()->days_of_week : [];
    
    return view('admin.city_routes.edit', compact(
        'route', 'carriers', 'cities', 'vehicles', 'daysOfWeek', 'selectedDaysOfWeek'
    ));
}

    /**
     * Update the specified city route
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Form data validation
            $validated = $request->validate([
                'carrier_id' => 'required|exists:carriers,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'line_number' => 'required|string|max:10',
                'line_color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
                'name' => 'required|string|max:255',
                'city_id' => 'required|exists:cities,id',
                'travel_time' => 'required|integer|min:1',
                'days_of_week' => 'required|array|min:1',
                'days_of_week.*' => 'required|integer|between:0,6',
                'is_active' => 'sometimes|boolean',
                'stops' => 'sometimes|array',
                'stops.*' => 'sometimes|exists:stops,id'
            ]);
            
            $route = Route::with('line', 'routeStops')->findOrFail($id);
            
            // 1. Update line
            $route->line->update([
                'carrier_id' => $request->carrier_id,
                'number' => $request->line_number,
                'name' => $request->name,
                'color' => $request->line_color
            ]);
            
            // 2. Update route
            $route->update([
                'name' => $request->name,
                'travel_time' => $request->travel_time,
                'is_active' => $request->boolean('is_active')
            ]);
            
            // 3. Update stops
            if (isset($request->stops) && is_array($request->stops) && count($request->stops) > 0) {
                // Delete existing stops
                $route->routeStops()->delete();
                
                // Add new stops
                $stopNumber = 1;
                $distanceFromStart = 0;
                
                foreach ($request->stops as $stopId) {
                    // Check if stop belongs to the selected city
                    $stop = Stop::where('id', $stopId)
                              ->where('city_id', $request->city_id)
                              ->firstOrFail();
                    
                    // Create route stop
                    RouteStop::create([
                        'route_id' => $route->id,
                        'stop_id' => $stopId,
                        'stop_number' => $stopNumber++,
                        'distance_from_start' => $distanceFromStart,
                        'time_to_next' => 5
                    ]);
                    
                    $distanceFromStart += 1000;
                }
            }
            
            // 4. Prepare days of week as an array of integers
            $daysOfWeek = collect($request->days_of_week ?? [])->map(fn($d) => (int)$d)->values()->all();
            
            // 5. Update schedule (delete old and create new)
            if ($route->schedules->isNotEmpty()) {
                $route->schedules()->delete();
            }
            
            $schedule = Schedule::create([
                'route_id' => $route->id,
                'days_of_week' => $daysOfWeek,
                'valid_from' => now(),
                'valid_to' => now()->addYear(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.city_routes.index')
                ->with('success', 'Kurs miejski został zaktualizowany pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update city route: ' . $e->getMessage());
            
            return back()->withInput()->withErrors(['general' => 'Błąd podczas aktualizacji kursu miejskiego: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified city route
     */
    public function destroy($id)
    {
        try {
            $route = Route::findOrFail($id);
            
            // Check if the route has any schedule or route stops associated with it
            if ($route->schedules()->count() > 0) {
                return redirect()->route('admin.city_routes.index')
                    ->with('error', 'Nie można usunąć trasy miejskiej, która ma przypisane rozkłady jazdy.');
            }
            
            // Check if the route has any route stops associated with it
            if ($route->routeStops()->count() > 0) {
                return redirect()->route('admin.city_routes.index')
                    ->with('error', 'Nie można usunąć trasy miejskiej, która ma przypisane przystanki.');
            }
            
            // Remove the route if it has no schedules or route stops
            $route->delete();
            
            return redirect()->route('admin.city_routes.index')
                ->with('success', 'Kurs miejski został usunięty pomyślnie');
        } catch (\Exception $e) {
            Log::error('Failed to delete city route: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Błąd podczas usuwania kursu miejskiego: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get stops for a city via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStopsForCity(Request $request)
    {
        try {
            $request->validate([
                'city_id' => 'required|exists:cities,id',
            ]);
            
            $stops = Stop::where('city_id', $request->city_id)
                ->orderBy('name')
                ->get(['id', 'name', 'address']);
                
            return response()->json([
                'success' => true,
                'stops' => $stops
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas pobierania przystanków.'
            ], 500);
        }
    }
}
