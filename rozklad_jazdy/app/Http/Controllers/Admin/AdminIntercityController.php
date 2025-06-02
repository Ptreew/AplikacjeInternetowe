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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminIntercityController extends Controller
{
    /**
     * Display a listing of the intercity routes
     */
    public function index()
    {
        // Get all intercity routes with pagination and eager loading
        $routes = Route::with(['line.carrier', 'routeStops.stop.city'])
                  ->whereHas('line', function($query) {
                      $query->where('number', 'NOT LIKE', 'M%'); // Filter out city routes (usually named with M prefix)
                  })
                  ->orderBy('id', 'desc')
                  ->paginate(10);
        
        return view('admin.intercity.index', compact('routes'));
    }

    /**
     * Show the form for creating a new intercity route
     */
    public function create()
    {
        $carriers = Carrier::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        
        // Get stops for each city
        $cityStops = [];
        foreach ($cities as $city) {
            $stops = Stop::where('city_id', $city->id)->get();
            $cityStops[$city->id] = $stops;
        }
        
        $daysOfWeek = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '0' => 'Niedziela',
        ];
        
        return view('admin.intercity.create', compact('carriers', 'cities', 'daysOfWeek', 'cityStops'));
    }

    /**
     * Store a newly created intercity route
     */
    public function store(Request $request)
    {
        Log::info('Intercity route creation request:', $request->all());
        
        // Validate main form data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'line_number' => 'required|string|max:10',
            'line_name' => 'required|string|max:255',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'origin_stop_id' => 'required|exists:stops,id',
            'destination_stop_id' => 'required|exists:stops,id|different:origin_stop_id',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|between:0,6',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create or find the line
            $line = Line::firstOrCreate(
                ['carrier_id' => $request->carrier_id, 'number' => $request->line_number],
                [
                    'name' => $request->line_name,
                    'color' => $request->line_color ?? '#0066CC',
                    'is_active' => true,
                ]
            );
            
            // Create the route
            $route = Route::create([
                'line_id' => $line->id,
                'name' => $request->origin_city_id . '-' . $request->destination_city_id,
                'is_active' => true,
            ]);
            
            // Add origin stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->origin_stop_id,
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 0,
            ]);
            
            // Add destination stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->destination_stop_id,
                'stop_number' => 2,
                'distance_from_start' => 100, // Default value in km
                'time_to_next' => 0,
            ]);
            
            // Create schedules for selected days
            foreach ($request->days_of_week as $day) {
                $schedule = Schedule::create([
                    'route_id' => $route->id,
                    'day_of_week' => $day,
                    'is_active' => true,
                ]);
                
                // Add departure for this schedule
                Departure::create([
                    'schedule_id' => $schedule->id,
                    'departure_time' => $request->departure_time,
                    'price' => $request->price,
                    'is_active' => true,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.intercity.index')
                ->with('success', 'Kurs międzymiastowy został dodany pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create intercity route: ' . $e->getMessage());
            
            return back()->withInput()->withErrors(['general' => 'Błąd podczas dodawania kursu międzymiastowego: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified intercity route
     */
    public function edit($id)
    {
        $route = Route::with(['line.carrier', 'routeStops.stop.city', 'schedules.departures'])
                ->findOrFail($id);
        
        $carriers = Carrier::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        
        // Get origin and destination stops
        $originStop = $route->routeStops->where('stop_number', 1)->first()->stop ?? null;
        $destinationStop = $route->routeStops->sortByDesc('stop_number')->first()->stop ?? null;
        
        // Get stops for each city
        $cityStops = [];
        foreach ($cities as $city) {
            $stops = Stop::where('city_id', $city->id)->get();
            $cityStops[$city->id] = $stops;
        }
        
        // Get schedule data
        $daysOfWeek = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '0' => 'Niedziela',
        ];
        $selectedDaysOfWeek = $route->schedules->pluck('day_of_week')->toArray();
        $departureTime = $route->schedules->first()->departures->first()->departure_time ?? '08:00';
        $arrivalTime = $route->schedules->first()->departures->first()->arrival_time ?? '10:00';
        $price = $route->schedules->first()->departures->first()->price ?? 0;
        
        return view('admin.intercity.edit', compact(
            'route', 'carriers', 'cities', 'originStop', 'destinationStop',
            'cityStops', 'selectedDaysOfWeek', 'daysOfWeek', 'departureTime', 'arrivalTime', 'price'
        ));
    }

    /**
     * Update the specified intercity route
     */
    public function update(Request $request, $id)
    {
        Log::info('Intercity route update request:', $request->all());
        
        // Validate main form data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'line_number' => 'required|string|max:10',
            'line_name' => 'required|string|max:255',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'origin_stop_id' => 'required|exists:stops,id',
            'destination_stop_id' => 'required|exists:stops,id|different:origin_stop_id',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|between:0,6',
        ]);
        
        try {
            DB::beginTransaction();
            
            $route = Route::findOrFail($id);
            
            // Update line information
            $line = $route->line;
            $line->carrier_id = $request->carrier_id;
            $line->number = $request->line_number;
            $line->name = $request->line_name;
            $line->save();
            
            // Update route stops
            $route->routeStops()->delete();
            
            // Add origin stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->origin_stop_id,
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 0,
            ]);
            
            // Add destination stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->destination_stop_id,
                'stop_number' => 2,
                'distance_from_start' => 100, // Default value in km
                'time_to_next' => 0,
            ]);
            
            // Update schedules and departures
            $route->schedules()->delete();
            
            foreach ($request->days_of_week as $day) {
                $schedule = Schedule::create([
                    'route_id' => $route->id,
                    'day_of_week' => $day,
                    'is_active' => true,
                ]);
                
                // Add departure for this schedule
                Departure::create([
                    'schedule_id' => $schedule->id,
                    'departure_time' => $request->departure_time,
                    'price' => $request->price,
                    'is_active' => true,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.intercity.index')
                ->with('success', 'Kurs międzymiastowy został zaktualizowany pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update intercity route: ' . $e->getMessage());
            
            return back()->withInput()->withErrors(['general' => 'Błąd podczas aktualizacji kursu międzymiastowego: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified intercity route
     */
    public function destroy($id)
    {
        try {
            $route = Route::findOrFail($id);
            
            DB::beginTransaction();
            
            // Delete all related records
            $route->routeStops()->delete();
            $route->schedules->each(function ($schedule) {
                $schedule->departures()->delete();
            });
            $route->schedules()->delete();
            
            // Delete the route
            $route->delete();
            
            DB::commit();
            
            return redirect()->route('admin.intercity.index')
                ->with('success', 'Kurs międzymiastowy został usunięty pomyślnie');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete intercity route: ' . $e->getMessage());
            
            return back()->withErrors(['general' => 'Błąd podczas usuwania kursu międzymiastowego: ' . $e->getMessage()]);
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
            
            $cityId = $request->city_id;
            $stops = Stop::where('city_id', $cityId)->get();
            
            if ($stops->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brak przystanków dla wybranego miasta',
                    'stops' => []
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Przystanki pobrane pomyślnie',
                'stops' => $stops
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Błąd podczas pobierania przystanków: ' . $e->getMessage(),
                'stops' => []
            ], 500);
        }
    }
}
