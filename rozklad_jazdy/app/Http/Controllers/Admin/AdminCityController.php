<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class AdminCityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::with('stops')->paginate(10);
        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Detailed debug logging
        \Log::info('================ FORM SUBMISSION DEBUG ================');
        \Log::info('All request data:', $request->all());
        \Log::info('Has voivodeship input: ' . ($request->has('voivodeship') ? 'YES' : 'NO'));
        \Log::info('Voivodeship input value: ' . $request->input('voivodeship'));
        \Log::info('POST parameters:', ['_POST' => $_POST]);
        \Log::info('Request method: ' . $request->method());
        \Log::info('=======================================================');
        
        // Attempt to resolve the issue
        if (!$request->has('voivodeship') || empty($request->input('voivodeship'))) {
            return back()
                ->withInput()
                ->withErrors(['voivodeship' => 'Pole województwo jest wymagane i nie może być puste.']);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'voivodeship' => 'required|string|max:255',
        ]);
        
        // Debugging validated data
        \Log::info('Validated data:', $validated);

        // Create city with explicit field assignment
        $city = new City();
        $city->name = $request->input('name');
        $city->voivodeship = $request->input('voivodeship');
        
        \Log::info('City przed zapisem:', [
            'name' => $city->name,
            'voivodeship' => $city->voivodeship
        ]);
        
        $city->save();
        
        \Log::info('Created city:', $city->toArray());

        return redirect()->route('admin.cities.index')->with('success', 'Miasto zostało dodane.');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        // Eager load the stops relation to avoid N+1 query problem
        $city->load('stops');
        
        return view('admin.cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'voivodeship' => 'required|string|max:255',
        ]);

        $city->update($validated);

        return redirect()->route('admin.cities.index')->with('success', 'Miasto zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'Miasto zostało usunięte.');
    }
}
