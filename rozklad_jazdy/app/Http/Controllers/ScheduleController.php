<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Middleware\CheckRole;

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
        $this->middleware(CheckRole::class . ':admin');
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
            // Check if the value is present in the days_of_week JSON array
            $query->whereJsonContains('days_of_week', (int)$request->day_of_week);
        }
        
        if ($request->has('valid_from')) {
            $query->where('valid_from', '>=', $request->valid_from);
        }
        
        if ($request->has('valid_to')) {
            $query->where('valid_to', '<=', $request->valid_to);
        }
        
        // Paginate the results
        $schedules = $query->orderBy('route_id')->paginate(15);
        
        // Get routes for filter dropdown
        $routes = Route::with('line')->orderBy('name')->get();
        
        // Day types for filters
        $dayTypes = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '0' => 'Niedziela'
        ];
        
        return view('admin.schedules.index', compact('schedules', 'routes', 'dayTypes'));
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
        
        return view('admin.schedules.create', compact('routes', 'dayTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'required|integer|between:0,6',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        
        // Create new schedule
        $schedule = new Schedule();
        $schedule->route_id = $validated['route_id'];
        $schedule->days_of_week = $validated['days_of_week'];
        $schedule->valid_from = $validated['valid_from'];
        $schedule->valid_to = $validated['valid_to'];
        $schedule->save();
        
        return redirect()->route('admin.schedules.show', $schedule)
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
        
        return view('admin.schedules.show', compact('schedule', 'dayTypes'));
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
        
        return view('admin.schedules.edit', compact('schedule', 'routes', 'dayTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Validate the request data
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'required|integer|between:0,6',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
        ]);
        
        // Update schedule
        $schedule->route_id = $validated['route_id'];
        $schedule->days_of_week = $validated['days_of_week'];
        $schedule->valid_from = $validated['valid_from'];
        $schedule->valid_to = $validated['valid_to'];
        $schedule->save();
        
        return redirect()->route('admin.schedules.show', $schedule)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        // Check if the schedule has any departures
        if ($schedule->departures()->exists()) {
            return redirect()->route('admin.schedules.show', $schedule)
                ->with('error', 'Cannot delete schedule with associated departures. Please delete the departures first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the schedule
            $schedule->delete();
            
            DB::commit();
            
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.schedules.show', $schedule)
                ->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}
