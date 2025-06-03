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
                      $query->whereNull('number'); // Intercity routes have NULL in number field
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
        
        // Get available active vehicles with line and carrier data
        $vehicles = Vehicle::with(['line.carrier'])
            ->whereHas('line', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('vehicle_number')
            ->get();
            
        $daysOfWeek = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '0' => 'Niedziela',
        ];
        
        return view('admin.intercity.create', compact('carriers', 'cities', 'daysOfWeek', 'cityStops', 'vehicles'));
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
            'line_number' => [
                'required',
                'string',
                'max:10',
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, 'M')) {
                        $fail('Numer linii międzymiastowej nie może zaczynać się od litery "M". Linie zaczynające się od "M" są zarezerwowane dla kursów miejskich.');
                    }
                },
            ],
            'line_name' => 'required|string|max:255',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'origin_stop_id' => 'required|exists:stops,id',
            'destination_stop_id' => 'required|exists:stops,id|different:origin_stop_id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'travel_time' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|between:0,6',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get origin and destination cities
            $originCity = \App\Models\City::find($request->origin_city_id);
            $destinationCity = \App\Models\City::find($request->destination_city_id);
            
            // Generate line name based on cities
            $lineName = $originCity->name . ' - ' . $destinationCity->name;
            
            // Create or find the line - for intercity routes, number is always NULL
            $line = Line::firstOrCreate(
                ['carrier_id' => $request->carrier_id, 'name' => $lineName],
                [
                    'number' => null, // Intercity routes MUST have NULL (not empty string) for number field
                    'color' => $request->line_color ?? '#0066CC',
                    'is_active' => true,
                ]
            );
            
            // Create the route
            $route = Route::create([
                'line_id' => $line->id,
                'name' => $originCity->name . ' - ' . $destinationCity->name,
                'travel_time' => $request->travel_time,
                'is_active' => $request->has('is_active'),
            ]);
            
            // Add origin stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->origin_stop_id,
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => $request->travel_time, // Time to next stop is the total travel time
            ]);
            
            // Add destination stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->destination_stop_id,
                'stop_number' => 2,
                'distance_from_start' => 100, // Default value in km
                'time_to_next' => 0,
            ]);
            
            // Prepare days_of_week as array of integers to match seeded data
            $daysOfWeek = collect($request->days_of_week ?? [])->map(fn($d) => (int)$d)->values()->all();
            
            // Create a single schedule with multiple days of week
            $schedule = Schedule::create([
                'route_id' => $route->id,
                'days_of_week' => $daysOfWeek,
                'valid_from' => now(),
                'valid_to' => now()->addYear(),
            ]);
            
            // Add departure for origin stop
            Departure::create([
                "stop_id" => $request->origin_stop_id,
                'schedule_id' => $schedule->id,
                'vehicle_id' => $request->vehicle_id,
                'departure_time' => $request->departure_time,
                'price' => $request->price,
                'is_active' => true, // Change in the future
            ]);
            
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
        
        // Get available active vehicles with line and carrier data
        $vehicles = Vehicle::with(['line.carrier'])
            ->whereHas('line', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('vehicle_number')
            ->get();
        
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
        // Get days of week from the first schedule
        $selectedDaysOfWeek = $route->schedules->first() ? $route->schedules->first()->days_of_week : [];
        // Get departure time from the first departure
        $departure = $route->schedules->first()->departures->first() ?? null;
        $departureTime = $departure ? date('H:i', strtotime($departure->departure_time)) : '08:00';
        
        // Get vehicle id from the first departure
        $departureVehicleId = $departure ? $departure->vehicle_id : null;
        $travel_time = $route->travel_time ?? 120;
        $price = $route->schedules->first()->departures->first()->price ?? 0;
        
        return view('admin.intercity.edit', compact(
            'route', 'carriers', 'cities', 'originStop', 'destinationStop',
            'cityStops', 'selectedDaysOfWeek', 'daysOfWeek', 'departureTime', 'travel_time', 'price', 'vehicles',
            'departureVehicleId'
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
            'line_number' => [
                'required',
                'string',
                'max:10',
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, 'M')) {
                        $fail('Numer linii międzymiastowej nie może zaczynać się od litery "M". Linie zaczynające się od "M" są zarezerwowane dla kursów miejskich.');
                    }
                },
            ],
            'line_name' => 'required|string|max:255',
            'origin_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:origin_city_id',
            'origin_stop_id' => 'required|exists:stops,id',
            'destination_stop_id' => 'required|exists:stops,id|different:origin_stop_id',
            'departure_time' => 'required|date_format:H:i',
            'travel_time' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|between:0,6',
        ]);
        
        try {
            DB::beginTransaction();
            
            $route = Route::findOrFail($id);
            
            // Get city names to automatically generate line name
            $originCity = \App\Models\City::find($request->origin_city_id);
            $destinationCity = \App\Models\City::find($request->destination_city_id);
            
            // Automatically generate line name based on cities
            $lineName = $originCity->name . ' - ' . $destinationCity->name;
            
            // Update line information
            $line = $route->line;
            $line->carrier_id = $request->carrier_id;
            $line->number = null; // Intercity routes always have NULL for number field
            $line->name = $lineName;
            $line->save();
            
            // Update route name to use city names instead of IDs
            $route->name = $lineName;
            $route->save();
            
            // Update route stops
            $route->routeStops()->delete();
            
            // Add origin stop
            RouteStop::create([
                'route_id' => $route->id,
                'stop_id' => $request->origin_stop_id,
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => $request->travel_time, // Time to next stop is the total travel time
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
            
            // Prepare days_of_week as array of integers to match seeded data
            $daysOfWeek = collect($request->days_of_week ?? [])->map(fn($d) => (int)$d)->values()->all();
            
            // Create a single schedule with multiple days of week
            $schedule = Schedule::create([
                'route_id' => $route->id,
                'days_of_week' => $daysOfWeek,
                'valid_from' => now(),
                'valid_to' => now()->addYear(),
            ]);
            
            // Add departure for origin stop
            Departure::create([
                'stop_id' => $request->origin_stop_id,
                'schedule_id' => $schedule->id,
                'vehicle_id' => $request->vehicle_id,
                'departure_time' => $request->departure_time,
                'price' => $request->price,
                'is_active' => true,
            ]);
            
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
