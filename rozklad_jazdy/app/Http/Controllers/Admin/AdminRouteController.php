<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Line;
use Illuminate\Http\Request;

class AdminRouteController extends Controller
{
    /**
     * Display a listing of the routes.
     */
    public function index()
    {
        $routes = Route::with(['line', 'line.carrier'])
                  ->where('type', 'intercity')
                  ->paginate(10);
        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create()
    {
        $lines = Line::with('carrier')->orderBy('name')->get();
        return view('admin.routes.create', compact('lines'));
    }

    /**
     * Store a newly created route in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'line_id' => 'required|exists:lines,id',
            'name' => 'required|string|max:255',
            'travel_time' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        // Dodanie typu trasy
        $data['type'] = 'intercity';

        Route::create($data);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Trasa została dodana pomyślnie.');
    }

    /**
     * Display the specified route.
     */
    public function show(Route $route)
    {
        $route->load(['line', 'line.carrier', 'routeStops.stop', 'schedules']);
        return view('admin.routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified route.
     */
    public function edit(Route $route)
    {
        $lines = Line::with('carrier')->orderBy('name')->get();
        return view('admin.routes.edit', compact('route', 'lines'));
    }

    /**
     * Update the specified route in storage.
     */
    public function update(Request $request, Route $route)
    {
        $request->validate([
            'line_id' => 'required|exists:lines,id',
            'name' => 'required|string|max:255',
            'travel_time' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        $route->update($data);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Trasa została zaktualizowana pomyślnie.');
    }

    /**
     * Remove the specified route from storage.
     */
    public function destroy(Route $route)
    {
        // Check if the route has any schedule or route stops associated with it
        if ($route->schedules()->count() > 0) {
            return redirect()->route('admin.routes.index')
                ->with('error', 'Nie można usunąć trasy, która ma przypisane rozkłady jazdy.');
        }

        if ($route->routeStops()->count() > 0) {
            return redirect()->route('admin.routes.index')
                ->with('error', 'Nie można usunąć trasy, która ma przypisane przystanki.');
        }

        $route->delete();

        return redirect()->route('admin.routes.index')
            ->with('success', 'Trasa została usunięta pomyślnie.');
    }
}
