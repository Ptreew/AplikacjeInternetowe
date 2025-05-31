<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods
        $this->middleware('auth');
        
        // Require admin role for all methods
        $this->middleware('role:admin');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = Schedule::with(['route.line.carrier']);
        
        if ($request->has('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        
        if ($request->has('day_type')) {
            $query->where('day_type', $request->day_type);
        }
        
        if ($request->has('valid_from')) {
            $query->where('valid_from', '>=', $request->valid_from);
        }
        
        if ($request->has('valid_to')) {
            $query->where('valid_to', '<=', $request->valid_to);
        }
        
        // Paginate the results
        $schedules = $query->orderBy('route_id')->orderBy('day_type')->paginate(15);
        
        // Get routes for filter dropdown
        $routes = Route::with('line')->orderBy('name')->get();
        
        // Day types for dropdown
        $dayTypes = [
            'weekday' => 'Dzień powszedni',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela', 
            'holiday' => 'Święto'
        ];
        
        return view('schedules.index', compact('schedules', 'routes', 'dayTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $routes = Route::with('line')->orderBy('name')->get();
        
        // Day types for dropdown
        $dayTypes = [
            'weekday' => 'Dzień powszedni',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
            'holiday' => 'Święto'
        ];
        
        return view('schedules.create', compact('routes', 'dayTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'day_type' => [
                'required',
                'in:weekday,saturday,sunday,holiday',
                Rule::unique('schedules')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                }),
            ],
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        
        // Create new schedule
        $schedule = new Schedule();
        $schedule->route_id = $validated['route_id'];
        $schedule->day_type = $validated['day_type'];
        $schedule->valid_from = $validated['valid_from'];
        $schedule->valid_to = $validated['valid_to'];
        $schedule->save();
        
        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule created successfully. Now you can add departures to this schedule.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        // Load related data
        $schedule->load([
            'route.line.carrier',
            'route.routeStops.stop.city',
            'departures' => function($query) {
                $query->orderBy('departure_time');
            },
            'departures.vehicle'
        ]);
        
        // Day types for display
        $dayTypes = [
            'weekday' => 'Dzień powszedni',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
            'holiday' => 'Święto'
        ];
        
        return view('schedules.show', compact('schedule', 'dayTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $routes = Route::with('line')->orderBy('name')->get();
        
        // Day types for dropdown
        $dayTypes = [
            'weekday' => 'Dzień powszedni',
            'saturday' => 'Sobota',
            'sunday' => 'Niedziela',
            'holiday' => 'Święto'
        ];
        
        return view('schedules.edit', compact('schedule', 'routes', 'dayTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'day_type' => [
                'required',
                'in:weekday,saturday,sunday,holiday',
                Rule::unique('schedules')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                })->ignore($schedule->id),
            ],
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        
        // Update schedule
        $schedule->route_id = $validated['route_id'];
        $schedule->day_type = $validated['day_type'];
        $schedule->valid_from = $validated['valid_from'];
        $schedule->valid_to = $validated['valid_to'];
        $schedule->save();
        
        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        // Check if the schedule has any departures
        if ($schedule->departures()->exists()) {
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'Cannot delete schedule with associated departures. Please delete the departures first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the schedule
            $schedule->delete();
            
            DB::commit();
            
            return redirect()->route('schedules.index')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('schedules.show', $schedule)
                ->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}
