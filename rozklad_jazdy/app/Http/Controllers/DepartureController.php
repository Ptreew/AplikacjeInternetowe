<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckRole;
use App\Models\Departure;
use App\Models\Schedule;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartureController extends Controller
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
        $query = Departure::with(['schedule.route.line', 'vehicle']);
        
        if ($request->has('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }
        
        if ($request->has('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->has('date')) {
            $query->whereDate('departure_time', $request->date);
        }
        
        
        // Paginate the results
        $departures = $query->orderBy('departure_time')->paginate(10);
        
        // Get schedules and vehicles for filter dropdowns
        $schedules = Schedule::with(['route.line'])->orderBy('route_id')->get();
        $vehicles = Vehicle::with(['line', 'line.carrier'])->orderBy('vehicle_number')->get();
        
        return view('admin.departures.index', compact('departures', 'schedules', 'vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Pre-select schedule if provided
        $selectedScheduleId = $request->schedule_id;
        $schedule = null;
        $stops = collect();
        
        if ($selectedScheduleId) {
            $schedule = Schedule::with(['route.routeStops.stop.city'])->find($selectedScheduleId);
            
            // Get stops for this schedule's route
            if ($schedule && $schedule->route) {
                $stops = $schedule->route->routeStops()
                    ->with('stop.city')
                    ->orderBy('stop_number', 'asc')
                    ->get()
                    ->map(function($routeStop) {
                        return $routeStop->stop;
                    });
            }
        }
        
        $schedules = Schedule::with(['route.line'])->get();
        $vehicles = Vehicle::with('line.carrier')->where('is_active', true)->orderBy('vehicle_number')->get();
        
        return view('admin.departures.create', compact('schedules', 'vehicles', 'selectedScheduleId', 'schedule', 'stops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'stop_id' => 'required|exists:stops,id',
            'departure_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        // Format the departure time properly
        $departureTime = Carbon::createFromFormat('H:i', $validated['departure_time']);
        
        try {
            DB::beginTransaction();
            
            // Create new departure
            $departure = new Departure();
            $departure->schedule_id = $validated['schedule_id'];
            $departure->vehicle_id = $validated['vehicle_id'];
            $departure->stop_id = $validated['stop_id'];
            $departure->departure_time = $departureTime;
            $departure->price = $validated['price'];
            $departure->is_active = $request->has('is_active');
            $departure->save();
            
            DB::commit();
            
            return redirect()->route('admin.schedules.show', $departure->schedule_id)
                ->with('success', 'Odjazd został pomyślnie dodany.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Błąd podczas dodawania odjazdu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Departure $departure)
    {
        // Load related data
        $departure->load([
            'schedule.route.line.carrier',
            'schedule.route.routeStops.stop.city',
            'vehicle.carrier',
            'tickets'
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
        
        return view('admin.departures.show', compact('departure', 'daysOfWeek'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departure $departure)
    {
        $departure->load('schedule.route');
        
        $schedules = Schedule::with(['route.line'])->get();
        $vehicles = Vehicle::with('line.carrier')->where('is_active', true)->orderBy('vehicle_number')->get();
        
        // Get stops for the current schedule's route
        $stops = collect();
        if ($departure->schedule && $departure->schedule->route) {
            $stops = $departure->schedule->route->routeStops()
                ->with('stop.city')
                ->orderBy('stop_number', 'asc')
                ->get()
                ->map(function($routeStop) {
                    return $routeStop->stop;
                });
        }
        
        // Format departure time for the form
        $departureTime = Carbon::parse($departure->departure_time)->format('H:i');
        
        return view('admin.departures.edit', compact('departure', 'schedules', 'vehicles', 'departureTime', 'stops'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departure $departure)
    {
        // Validate the request data
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'stop_id' => 'required|exists:stops,id',
            'price' => 'required|numeric|min:0',
            'departure_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
        ]);
        
        // Format the departure time properly
        $departureTime = Carbon::createFromFormat('H:i', $validated['departure_time']);
        
        try {
            DB::beginTransaction();
            
            // Update departure
            $departure->schedule_id = $validated['schedule_id'];
            $departure->vehicle_id = $validated['vehicle_id'];
            $departure->stop_id = $validated['stop_id'];
            $departure->price = $validated['price'];
            $departure->departure_time = $departureTime;
            $departure->is_active = $request->has('is_active');
            $departure->save();
            
            DB::commit();
            
            return redirect()->route('admin.schedules.show', $departure->schedule_id)
                ->with('success', 'Odjazd został pomyślnie zaktualizowany.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Błąd podczas aktualizacji odjazdu: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departure $departure)
    {
        $scheduleId = $departure->schedule_id;
        
        // Check if there are any tickets with status other than cancelled or used
        // (i.e., tickets that are reserved or paid)
        if ($departure->tickets()->whereNotIn('status', ['cancelled', 'used'])->exists()) {
            return redirect()->route('admin.departures.show', $departure)
                ->with('error', 'Nie można usunąć odjazdu, ponieważ są do niego przypisane aktywne bilety (zarezerwowane lub opłacone). Najpierw anuluj lub oznacz jako wykorzystane wszystkie bilety.');
        }
        
        try {
            DB::beginTransaction();
            
            // Remove all cancelled or used tickets, if any
            $departure->tickets()->whereIn('status', ['cancelled', 'used'])->delete();
            
            // Delete the departure
            $departure->delete();
            
            DB::commit();
            
            return redirect()->route('admin.schedules.show', $scheduleId)
                ->with('success', 'Odjazd został pomyślnie usunięty.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.departures.show', $departure)
                ->with('error', 'Błąd podczas usuwania odjazdu: ' . $e->getMessage());
        }
    }
}
