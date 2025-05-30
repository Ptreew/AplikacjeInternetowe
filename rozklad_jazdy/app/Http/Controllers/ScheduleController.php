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
        
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active == 1);
        }
        
        // Paginate the results
        $schedules = $query->orderBy('route_id')->orderBy('day_of_week')->paginate(15);
        
        // Get routes for filter dropdown
        $routes = Route::with('line')->orderBy('name')->get();
        
        // Days of week for dropdown
        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return view('schedules.index', compact('schedules', 'routes', 'daysOfWeek'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $routes = Route::with('line')->where('is_active', true)->orderBy('name')->get();
        
        // Days of week for dropdown
        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return view('schedules.create', compact('routes', 'daysOfWeek'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'day_of_week' => [
                'required',
                'integer',
                'min:0',
                'max:6',
                Rule::unique('schedules')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                }),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Create new schedule
        $schedule = new Schedule();
        $schedule->route_id = $validated['route_id'];
        $schedule->day_of_week = $validated['day_of_week'];
        $schedule->is_active = $request->has('is_active');
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
        
        // Days of week for display
        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return view('schedules.show', compact('schedule', 'daysOfWeek'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $routes = Route::with('line')->where('is_active', true)->orderBy('name')->get();
        
        // Days of week for dropdown
        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        
        return view('schedules.edit', compact('schedule', 'routes', 'daysOfWeek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'day_of_week' => [
                'required',
                'integer',
                'min:0',
                'max:6',
                Rule::unique('schedules')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->route_id);
                })->ignore($schedule->id),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Update schedule
        $schedule->route_id = $validated['route_id'];
        $schedule->day_of_week = $validated['day_of_week'];
        $schedule->is_active = $request->has('is_active');
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
