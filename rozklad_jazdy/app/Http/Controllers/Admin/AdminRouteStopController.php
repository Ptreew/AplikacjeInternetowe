<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Stop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRouteStopController extends Controller
{
    /**
     * Store a newly created route stop in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'required|exists:stops,id',
            'stop_number' => 'required|integer|min:1',
            'distance_from_start' => 'nullable|numeric|min:0',
            'time_to_next' => 'nullable|integer|min:0',
        ]);
        
        // Konwersja odległości z km na metry
        if ($request->has('distance_from_start') && $request->distance_from_start !== null) {
            $request->merge(['distance_from_start' => $request->distance_from_start * 1000]);
        }

        // Sprawdź, czy przystanek nie jest już dodany do tej trasy
        $exists = RouteStop::where('route_id', $request->route_id)
            ->where('stop_id', $request->stop_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Ten przystanek jest już dodany do trasy.');
        }

        // Jeśli chcemy wstawić przystanek w środku kolejności, przesuwamy numery kolejnych przystanków
        DB::transaction(function () use ($request) {
            $existingStops = RouteStop::where('route_id', $request->route_id)
                ->where('stop_number', '>=', $request->stop_number)
                ->lockForUpdate()
                ->get();

            foreach ($existingStops as $stop) {
                $stop->update(['stop_number' => $stop->stop_number + 1]);
            }

            RouteStop::create($request->all());
        });

        return redirect()->back()
            ->with('success', 'Przystanek został dodany do trasy.');
    }

    /**
     * Update the specified route stop in storage.
     */
    public function update(Request $request, RouteStop $routeStop)
    {
        $request->validate([
            'stop_number' => 'required|integer|min:1',
            'distance_from_start' => 'nullable|numeric|min:0',
            'time_to_next' => 'nullable|integer|min:0',
        ]);
        
        // Konwersja odległości z km na metry
        if ($request->has('distance_from_start') && $request->distance_from_start !== null) {
            $request->merge(['distance_from_start' => $request->distance_from_start * 1000]);
        }

        // Jeśli zmieniamy kolejność, musimy zaktualizować pozostałe przystanki
        DB::transaction(function () use ($request, $routeStop) {
            if ($request->stop_number != $routeStop->stop_number) {
                if ($request->stop_number > $routeStop->stop_number) {
                    // Przesuwamy w dół
                    RouteStop::where('route_id', $routeStop->route_id)
                        ->where('stop_number', '>', $routeStop->stop_number)
                        ->where('stop_number', '<=', $request->stop_number)
                        ->decrement('stop_number');
                } else {
                    // Przesuwamy w górę
                    RouteStop::where('route_id', $routeStop->route_id)
                        ->where('stop_number', '<', $routeStop->stop_number)
                        ->where('stop_number', '>=', $request->stop_number)
                        ->increment('stop_number');
                }
            }

            $routeStop->update($request->all());
        });

        return redirect()->back()
            ->with('success', 'Przystanek został zaktualizowany.');
    }

    /**
     * Remove the specified route stop from storage.
     */
    public function destroy(RouteStop $routeStop)
    {
        $route_id = $routeStop->route_id;
        $stop_number = $routeStop->stop_number;

        $routeStop->delete();

        // Zaktualizuj numery kolejności dla pozostałych przystanków
        RouteStop::where('route_id', $route_id)
            ->where('stop_number', '>', $stop_number)
            ->decrement('stop_number');

        return redirect()->back()
            ->with('success', 'Przystanek został usunięty z trasy.');
    }
}
