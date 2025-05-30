<?php

namespace App\Http\Controllers;

use App\Models\Stop;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StopController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods except index and show
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
        $query = Stop::with('city');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active == 1);
        }
        
        // Paginate the results
        $stops = $query->orderBy('city_id')->orderBy('name')->paginate(15);
        
        // Get cities for filter dropdown
        $cities = City::orderBy('name')->get();
        
        return view('stops.index', compact('stops', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cities = City::orderBy('name')->get();
        return view('stops.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stops')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                }),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('stops')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                }),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Create new stop
        $stop = new Stop();
        $stop->city_id = $validated['city_id'];
        $stop->name = $validated['name'];
        $stop->code = $validated['code'];
        $stop->is_active = $request->has('is_active');
        $stop->save();
        
        return redirect()->route('stops.show', $stop)
            ->with('success', 'Stop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stop $stop)
    {
        // Load related data
        $stop->load([
            'city',
            'routeStops.route.line.carrier'
        ]);
        
        return view('stops.show', compact('stop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stop $stop)
    {
        $cities = City::orderBy('name')->get();
        return view('stops.edit', compact('stop', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stop $stop)
    {
        // Validate the request data
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stops')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                })->ignore($stop->id),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('stops')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->city_id);
                })->ignore($stop->id),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Update stop
        $stop->city_id = $validated['city_id'];
        $stop->name = $validated['name'];
        $stop->code = $validated['code'];
        $stop->is_active = $request->has('is_active');
        $stop->save();
        
        return redirect()->route('stops.show', $stop)
            ->with('success', 'Stop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stop $stop)
    {
        // Check if the stop is used in any route
        if ($stop->routeStops()->exists()) {
            return redirect()->route('stops.show', $stop)
                ->with('error', 'Cannot delete stop that is used in routes. Please remove the stop from all routes first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the stop
            $stop->delete();
            
            DB::commit();
            
            return redirect()->route('stops.index')
                ->with('success', 'Stop deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('stops.show', $stop)
                ->with('error', 'Failed to delete stop: ' . $e->getMessage());
        }
    }
}
