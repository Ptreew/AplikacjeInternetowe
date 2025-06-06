<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Carrier;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods
        $this->middleware('auth');
        
        // Require admin role for create, store, edit, update, and destroy methods
        $this->middleware(CheckRole::class . ':admin')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = Vehicle::with(['carrier']);
        
        if ($request->has('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }
        
        if ($request->has('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }
        
        if ($request->has('number')) {
            $query->where('number', 'like', '%' . $request->number . '%');
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active == 1);
        }
        
        // Paginate the results
        $vehicles = $query->orderBy('carrier_id')->orderBy('number')->paginate(15);
        
        // Get carriers for filter dropdown
        $carriers = Carrier::orderBy('name')->get();
        
        return view('vehicles.index', compact('vehicles', 'carriers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.create', compact('carriers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'type' => 'required|string|max:255',
            'number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'is_active' => 'boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create new vehicle
            $vehicle = new Vehicle();
            $vehicle->carrier_id = $validated['carrier_id'];
            $vehicle->type = $validated['type'];
            $vehicle->number = $validated['number'];
            $vehicle->capacity = $validated['capacity'];
            $vehicle->year = $validated['year'];
            $vehicle->is_active = $request->has('is_active');
            $vehicle->save();
            
            DB::commit();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Vehicle created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to create vehicle: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        // Load related data
        $vehicle->load([
            'carrier',
            'departures' => function($query) {
                $query->orderBy('departure_time');
            },
            'departures.schedule.route.line'
        ]);
        
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.edit', compact('vehicle', 'carriers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Validate the request data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'type' => 'required|string|max:255',
            'number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'is_active' => 'boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update vehicle
            $vehicle->carrier_id = $validated['carrier_id'];
            $vehicle->type = $validated['type'];
            $vehicle->number = $validated['number'];
            $vehicle->capacity = $validated['capacity'];
            $vehicle->year = $validated['year'];
            $vehicle->is_active = $request->has('is_active');
            $vehicle->save();
            
            DB::commit();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to update vehicle: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Check if the vehicle has any departures
        if ($vehicle->departures()->exists()) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Cannot delete vehicle with associated departures. Please remove the vehicle from all departures first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the vehicle
            $vehicle->delete();
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }
}
