<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Departure;
use Illuminate\Http\Request;

class AdminScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::with(['route.line.carrier'])
                            ->orderBy('id', 'desc')
                            ->paginate(10);
        
        return view('admin.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $routes = Route::with(['line.carrier'])
                ->where('active', true)
                ->orderBy('id', 'desc')
                ->get();
        
        return view('admin.schedules.create', compact('routes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|min:0|max:6',
        ]);

        $schedule = Schedule::create($request->all());

        return redirect()->route('admin.schedules.show', $schedule)
            ->with('success', 'Rozkład został utworzony pomyślnie.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['route.line.carrier', 'route.routeStops.stop', 'departures']);
        
        // Uporządkuj przystanki według kolejności
        $schedule->route->routeStops = $schedule->route->routeStops->sortBy('stop_number');
        
        // Uporządkuj odjazdy według czasu
        $schedule->departures = $schedule->departures->sortBy('departure_time');
        
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $routes = Route::with(['line.carrier'])
                ->where('active', true)
                ->orderBy('id', 'desc')
                ->get();
        
        return view('admin.schedules.edit', compact('schedule', 'routes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|integer|min:0|max:6',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.show', $schedule)
            ->with('success', 'Rozkład został zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        // Sprawdź, czy istnieją powiązane bilety
        $hasTickets = $schedule->departures()->whereHas('tickets')->exists();
        
        if ($hasTickets) {
            return redirect()->back()
                ->with('error', 'Nie można usunąć rozkładu, ponieważ istnieją powiązane bilety.');
        }
        
        // Usuń najpierw wszystkie odjazdy powiązane z tym rozkładem
        $schedule->departures()->delete();
        
        // Następnie usuń sam rozkład
        $schedule->delete();
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Rozkład został usunięty pomyślnie.');
    }
}
