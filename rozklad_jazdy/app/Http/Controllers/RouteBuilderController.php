<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Line;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use App\Models\Stop;
use App\Models\Departure;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class RouteBuilderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication
        $this->middleware('auth');
        
        // Require admin role
        $this->middleware(\App\Http\Middleware\CheckRole::class.':admin');
    }
    
    /**
     * Show the first step of route creation (basic information)
     */
    public function showStep1()
    {
        // Get lines for dropdown
        $lines = Line::with('carrier')->orderBy('name')->get();
        
        // Check which lines have active vehicles
        $linesWithVehicles = Vehicle::select('line_id')
            ->where('is_active', true)
            ->distinct()
            ->pluck('line_id')
            ->toArray();
        
        return view('admin.routes.builder.step1', compact('lines', 'linesWithVehicles'));
    }
    
    /**
     * Process step 1 and move to step 2
     */
    public function processStep1(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => 'required|exists:lines,id',
            'type' => 'required|in:city,intercity',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes')->where(function ($query) use ($request) {
                    return $query->where('line_id', $request->line_id);
                }),
            ],
            'is_active' => 'boolean',
            'travel_time' => 'nullable|integer|min:1',
        ]);
        
        // Store validated data in session for the next step
        Session::put('route_builder', [
            'basic_info' => $validated,
            'current_step' => 2
        ]);
        
        return redirect()->route('admin.routes.builder.step2');
    }
    
    /**
     * Show the second step (adding stops)
     */
    public function showStep2()
    {
        // Check if we have data from step 1
        if (!Session::has('route_builder.basic_info')) {
            return redirect()->route('admin.routes.builder.step1')
                ->with('error', 'Najpierw wypełnij podstawowe informacje o trasie.');
        }
        
        // Ensure we are following the correct step order
        if (!Session::has('route_builder.current_step') || Session::get('route_builder.current_step') < 2) {
            Session::put('route_builder.current_step', 2);
        }
        
        $routeData = Session::get('route_builder');
        $routeType = $routeData['basic_info']['type'];
        
        // Load stops based on route type
        if ($routeType == 'city') {
            // For city routes, show stops by city
            $cities = City::orderBy('name')->get();
            $stops = []; // Will be loaded via AJAX based on selected city
            
            return view('admin.routes.builder.step2_city', compact('cities', 'stops', 'routeData'));
        } else {
            // For intercity routes, show all cities and their stops
            $cities = City::with('stops')->orderBy('name')->get();
            
            return view('admin.routes.builder.step2_intercity', compact('cities', 'routeData'));
        }
    }
    
    /**
     * Process step 2 and move to step 3
     */
    public function processStep2(Request $request)
    {
        // Check if we have data from step 1
        if (!Session::has('route_builder.basic_info')) {
            return redirect()->route('admin.routes.builder.step1')
                ->with('error', 'Najpierw wypełnij podstawowe informacje o trasie.');
        }
        
        // Validate that we have at least 2 stops
        $validated = $request->validate([
            'stop_ids' => 'required|array|min:2',
            'stop_ids.*' => 'required|exists:stops,id',
            'time_to_next' => 'required|array|min:1',
            'time_to_next.*' => 'nullable|integer|min:0',
            'distance_from_start' => 'required|array|min:2',
            'distance_from_start.*' => 'nullable|numeric|min:0',
        ]);
        
        // Calculate total travel time if not provided in step 1
        $routeData = Session::get('route_builder');
        if (empty($routeData['basic_info']['travel_time'])) {
            $routeData['basic_info']['travel_time'] = array_sum(array_filter($validated['time_to_next']));
        }
        
        // Store stops data and navigate to step 3
        $routeData['stops_data'] = $validated;
        $routeData['current_step'] = 3;
        Session::put('route_builder', $routeData);
        
        return redirect()->route('admin.routes.builder.step3');
    }
    
    /**
     * Show the third step (create schedules)
     */
    public function showStep3()
    {
        // Check if we have data from previous steps
        if (!Session::has('route_builder.basic_info')) {
            return redirect()->route('admin.routes.builder.step1')
                ->with('error', 'Musisz zacząć od wprowadzenia podstawowych informacji o trasie.');
        }
        
        if (!Session::has('route_builder.stops_data')) {
            return redirect()->route('admin.routes.builder.step2')
                ->with('error', 'Najpierw musisz dodać przystanki do trasy.');
        }
        
        // Check if we are following the correct step order
        if (!Session::has('route_builder.current_step') || Session::get('route_builder.current_step') < 3) {
            // Update current_step to 3 if all data is available
            Session::put('route_builder.current_step', 3);
        }
        
        $routeData = Session::get('route_builder');
        
        // Get stops data for display
        $stopIds = $routeData['stops_data']['stop_ids'];
        $stops = Stop::whereIn('id', $stopIds)->get()->keyBy('id');
        
        $stopsSequence = [];
        foreach ($stopIds as $index => $stopId) {
            $stopsSequence[] = [
                'stop' => $stops[$stopId],
                'time_to_next' => $index < count($stopIds) - 1 ? $routeData['stops_data']['time_to_next'][$index] : null,
                'distance_from_start' => $routeData['stops_data']['distance_from_start'][$index],
            ];
        }
        
        // Load vehicles for schedule creation - tylko aktywne pojazdy dla wybranej linii
        $basicInfo = $routeData['basic_info'];
        $lineId = $basicInfo['line_id'];
        $vehicles = Vehicle::where('line_id', $lineId)
            ->where('is_active', true)
            ->get();
        
        // Load line information for display in the form
        $line = Line::findOrFail($lineId);
        
        return view('admin.routes.builder.step3', compact('routeData', 'stopsSequence', 'vehicles', 'line'));
    }
    
    /**
     * Process step 3 and finalize route creation
     */
    public function processStep3(Request $request)
    {
        // Add advanced logging of incoming data
        \Log::info('================ START OF SAVING PROCESS ================');
        \Log::info('Client IP address: ' . $request->ip());
        \Log::info('HTTP method: ' . $request->method());
        \Log::info('HTTP headers: ', $request->headers->all());
        
        // Detailed logging of POST data
        \Log::info('Wszystkie dane POST:', $request->post());
        \Log::info('Dane schedule z formularza:', $request->input('schedules', 'BRAK DANYCH SCHEDULE'));
        \Log::info('Dane sesji route_builder:', Session::get('route_builder', 'BRAK DANYCH SESJI'));
        
        // Check if CSRF token is present
        \Log::info('Token CSRF istnieje: ' . ($request->has('_token') ? 'TAK' : 'NIE'));
        
        // Check if we have data from previous steps
        if (!Session::has('route_builder.stops_data') || !Session::has('route_builder.basic_info')) {
            \Log::error('Brakujące dane sesji w processStep3');
            return redirect()->route('admin.routes.builder.step1')
                ->with('error', 'Proces tworzenia trasy nie został poprawnie rozpoczęty.');
        }
        
        try {
            // Dump form data for debugging
            \Log::info('Dane formularza przed walidacją:', ['form_data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'schedules' => 'required|array|min:1',
                'schedules.*.days_of_week' => 'required|array|min:1',
                'schedules.*.days_of_week.*' => 'required|integer|between:0,6',
                'schedules.*.valid_from' => 'required|date',
                'schedules.*.valid_to' => 'required|date',
                'schedules.*.departures' => 'required|array|min:1',
                'schedules.*.departures.*.departure_time' => 'required|date_format:H:i',
                'schedules.*.departures.*.vehicle_id' => 'required|exists:vehicles,id',
                'schedules.*.departures.*.price' => 'required|numeric|min:0',
            ]);
            
            // Additional date validation (end date must be after start date)
            foreach ($request->input('schedules', []) as $key => $schedule) {
                if (isset($schedule['valid_from']) && isset($schedule['valid_to'])) {
                    $from = Carbon::parse($schedule['valid_from']);
                    $to = Carbon::parse($schedule['valid_to']);
                    
                    if ($from->greaterThanOrEqualTo($to)) {
                        $validator->errors()->add("schedules.{$key}.valid_to", 
                            'Data zakończenia musi być późniejsza niż data rozpoczęcia.');
                    }
                }
            }
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            $validated = $validator->validated();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd walidacji: ' . $e->getMessage())
                ->withInput();
        }
        
        // Retrieve route data from session
        $routeData = Session::get('route_builder');
        $basicInfo = $routeData['basic_info'];
        $stopsData = $routeData['stops_data'];
        
        // Begin a database transaction
        DB::beginTransaction();
        
        try {
            // Step 1: Create the route
            $route = Route::create([
                'line_id' => $basicInfo['line_id'],
                'type' => $basicInfo['type'],
                'name' => $basicInfo['name'],
                // Automatically calculate travel time based on stop times
                'travel_time' => array_sum(array_filter($stopsData['time_to_next'] ?? [])),
                'is_active' => $basicInfo['is_active'] ?? true, // Default to active route
            ]);
            
            // Step 2: Create route stops
            foreach ($stopsData['stop_ids'] as $index => $stopId) {
                RouteStop::create([
                    'route_id' => $route->id,
                    'stop_id' => $stopId,
                    'stop_number' => $index + 1, // 1-based indexing for stop sequence
                    'time_to_next' => $index < count($stopsData['stop_ids']) - 1 ? $stopsData['time_to_next'][$index] : 0, // For the last stop, must be a non-NULL value
                    // Convert km to m (×1000) for database storage
                    'distance_from_start' => $stopsData['distance_from_start'][$index] * 1000,
                ]);
            }
            
            // Step 3: Create schedules and departures
            foreach ($validated['schedules'] as $scheduleData) {
                $schedule = Schedule::create([
                    'route_id' => $route->id,
                    'days_of_week' => $scheduleData['days_of_week'],
                    'valid_from' => $scheduleData['valid_from'],
                    'valid_to' => $scheduleData['valid_to'],
                ]);
                
                foreach ($scheduleData['departures'] as $departureData) {
                    // Use the first stop as the departure location
                    $firstStopId = RouteStop::where('route_id', $route->id)
                        ->orderBy('stop_number', 'asc')
                        ->first()->stop_id;
                        
                    Departure::create([
                        'schedule_id' => $schedule->id,
                        'departure_time' => $departureData['departure_time'],
                        'vehicle_id' => $departureData['vehicle_id'],
                        'stop_id' => $firstStopId, // First stop as departure location
                        'price' => $departureData['price'] ?? 0.00, // Use value from form or default
                        'is_active' => true
                    ]);
                }
            }
            
            // Commit transaction
            DB::commit();
            
            // Clear session data
            Session::forget('route_builder');
            
            return redirect()->route('admin.routes.show', $route)
                ->with('success', 'Trasa została pomyślnie utworzona wraz z przystankami i harmonogramem (' . $route->id . ').');
            
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();
            
            // Error debugging
            $errorDetails = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
            
            // Save error details to log
            \Log::error('Błąd podczas tworzenia trasy:', $errorDetails);
            
            return redirect()->route('admin.routes.builder.step3')
                ->with('error', 'Wystąpił błąd podczas tworzenia trasy: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Get stops by city (AJAX)
     */
    public function getStopsByCity($cityId)
    {
        $stops = Stop::where('city_id', $cityId)
            ->orderBy('name')
            ->get();
            
        return response()->json($stops);
    }
    
    /**
     * Cancel route creation and clear session data
     */
    public function cancel()
    {
        Session::forget('route_builder');
        
        return redirect()->route('admin.routes.index')
            ->with('info', 'Tworzenie trasy zostało anulowane.');
    }
}
