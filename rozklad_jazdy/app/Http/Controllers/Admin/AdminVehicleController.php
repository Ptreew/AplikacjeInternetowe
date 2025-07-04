<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Line;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     */
    public function index()
    {
        $vehicles = Vehicle::with(['line', 'line.carrier'])->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $lines = Line::orderBy('name')->get();
        return view('admin.vehicles.create', compact('lines'));
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'line_id' => 'required|exists:lines,id',
            'vehicle_number' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }
        
        // Obsługa przesyłanego zdjęcia
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('vehicles', 'public');
                $data['image_path'] = $imagePath;
            } catch (\Exception $e) {
                // Logowanie błędu
                \Log::error('Błąd przesyłania zdjęcia: ' . $e->getMessage());
                return redirect()->back()->withInput()
                    ->with('error', 'Błąd przesyłania zdjęcia: ' . $e->getMessage());
            }
        }

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Pojazd został dodany pomyślnie.');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['line', 'line.carrier', 'departures']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        $lines = Line::orderBy('name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'lines'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'line_id' => 'required|exists:lines,id',
            'vehicle_number' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }
        
        // Obsługa przesyłanego zdjęcia
        if ($request->hasFile('image')) {
            try {
                // Usuń stare zdjęcie jeśli istnieje
                if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
                    Storage::disk('public')->delete($vehicle->image_path);
                }
                
                $imagePath = $request->file('image')->store('vehicles', 'public');
                $data['image_path'] = $imagePath;
            } catch (\Exception $e) {
                // Logowanie błędu
                \Log::error('Błąd przesyłania zdjęcia: ' . $e->getMessage());
                return redirect()->back()->withInput()
                    ->with('error', 'Błąd przesyłania zdjęcia: ' . $e->getMessage());
            }
        }

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Pojazd został zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Check if the vehicle has any departures scheduled
        if ($vehicle->departures()->count() > 0) {
            return redirect()->route('admin.vehicles.index')
                ->with('error', 'Nie można usunąć pojazdu, który ma zaplanowane odjazdy.');
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Pojazd został usunięty pomyślnie.');
    }
}
