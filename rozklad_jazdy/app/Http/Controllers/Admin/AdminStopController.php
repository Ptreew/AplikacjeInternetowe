<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stop;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminStopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all stops with pagination and eager loading of city relationship
        $stops = Stop::with('city')->paginate(10);
        
        return view('admin.stops.index', compact('stops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all cities for the dropdown
        $cities = City::orderBy('name')->get();
        
        return view('admin.stops.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Stop creation request data:', $request->all());
        
        // Validate input
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);
        
        // Debug validated data
        Log::info('Validated stop data:', $validated);
        
        // Create stop with explicit field assignment
        $stop = new Stop();
        $stop->city_id = $request->input('city_id');
        $stop->name = $request->input('name');
        $stop->code = $request->input('code');
        $stop->is_active = $request->has('is_active');
        $stop->save();
        
        Log::info('Created stop:', $stop->toArray());
        
        return redirect()->route('admin.stops.index')->with('success', 'Przystanek został dodany.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stop $stop)
    {
        // Get all cities for the dropdown
        $cities = City::orderBy('name')->get();
        
        return view('admin.stops.edit', compact('stop', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stop $stop)
    {
        // Debug logging
        Log::info('Stop update request data:', $request->all());
        
        // Validate input
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);
        
        // Debug validated data
        Log::info('Validated stop data for update:', $validated);
        
        // Update stop with explicit field assignment
        $stop->city_id = $request->input('city_id');
        $stop->name = $request->input('name');
        $stop->code = $request->input('code');
        $stop->is_active = $request->has('is_active');
        $stop->save();
        
        Log::info('Updated stop:', $stop->toArray());
        
        return redirect()->route('admin.stops.index')->with('success', 'Przystanek został zaktualizowany.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stop $stop)
    {
        $stop->delete();
        return redirect()->route('admin.stops.index')->with('success', 'Przystanek został usunięty.');
    }
}
