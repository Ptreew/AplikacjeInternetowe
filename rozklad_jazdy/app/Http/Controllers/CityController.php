<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods except index and show
        $this->middleware('auth')->except(['index', 'show']);
        
        // Require admin role for create, store, edit, update, destroy
        $this->middleware(CheckRole::class . ':admin')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = City::query();
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Paginate the results
        $cities = $query->orderBy('name')->paginate(15);
        
        return view('cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
        ]);
        
        // Create new city
        $city = new City();
        $city->name = $validated['name'];
        $city->save();
        
        return redirect()->route('cities.show', $city)
            ->with('success', 'City created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        // Load related stops
        $city->load([
            'stops' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }
        ]);
        
        return view('cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities')->ignore($city->id),
            ],
        ]);
        
        // Update city
        $city->name = $validated['name'];
        $city->save();
        
        return redirect()->route('cities.show', $city)
            ->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        // Check if the city has any stops
        if ($city->stops()->exists()) {
            return redirect()->route('cities.show', $city)
                ->with('error', 'Cannot delete city with associated stops. Please delete the stops first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the city
            $city->delete();
            
            DB::commit();
            
            return redirect()->route('cities.index')
                ->with('success', 'City deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('cities.show', $city)
                ->with('error', 'Failed to delete city: ' . $e->getMessage());
        }
    }
}
