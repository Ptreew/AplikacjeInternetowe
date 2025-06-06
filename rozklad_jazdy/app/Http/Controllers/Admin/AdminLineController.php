<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Carrier;
use Illuminate\Http\Request;

class AdminLineController extends Controller
{
    /**
     * Display a listing of the lines.
     */
    public function index()
    {
        $lines = Line::with('carrier')->paginate(10);
        return view('admin.lines.index', compact('lines'));
    }

    /**
     * Show the form for creating a new line.
     */
    public function create()
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('admin.lines.create', compact('carriers'));
    }

    /**
     * Store a newly created line in storage.
     */
    public function store(Request $request)
    {
        // Dostosowanie walidacji w zależności od typu linii
        $validationRules = [
            'carrier_id' => 'required|exists:carriers,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
            'line_type' => 'required|in:city,intercity',
        ];

        // Numer linii jest wymagany tylko dla linii miejskich
        if ($request->line_type === 'city') {
            $validationRules['number'] = 'required|string|max:10';
        }

        $request->validate($validationRules);

        $data = $request->except('line_type');
        
        // Dla kursów międzymiastowych ustawiamy numer na null
        if ($request->line_type === 'intercity') {
            $data['number'] = null;
        }
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        Line::create($data);

        return redirect()->route('admin.lines.index')
            ->with('success', 'Linia została dodana pomyślnie.');
    }

    /**
     * Display the specified line.
     */
    public function show(Line $line)
    {
        $line->load(['carrier', 'routes', 'vehicles']);
        return view('admin.lines.show', compact('line'));
    }

    /**
     * Show the form for editing the specified line.
     */
    public function edit(Line $line)
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('admin.lines.edit', compact('line', 'carriers'));
    }

    /**
     * Update the specified line in storage.
     */
    public function update(Request $request, Line $line)
    {
        // Dostosowanie walidacji w zależności od typu linii
        $validationRules = [
            'carrier_id' => 'required|exists:carriers,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
            'line_type' => 'required|in:city,intercity',
        ];

        // Numer linii jest wymagany tylko dla linii miejskich
        if ($request->line_type === 'city') {
            $validationRules['number'] = 'required|string|max:10';
        }

        $request->validate($validationRules);

        $data = $request->except('line_type');
        
        // Dla kursów międzymiastowych ustawiamy numer na null
        if ($request->line_type === 'intercity') {
            $data['number'] = null;
        }
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        $line->update($data);

        return redirect()->route('admin.lines.index')
            ->with('success', 'Linia została zaktualizowana pomyślnie.');
    }

    /**
     * Remove the specified line from storage.
     */
    public function destroy(Line $line)
    {
        // Check if the line has any routes or vehicles associated with it
        if ($line->routes()->count() > 0) {
            return redirect()->route('admin.lines.index')
                ->with('error', 'Nie można usunąć linii, która ma przypisane trasy.');
        }

        if ($line->vehicles()->count() > 0) {
            return redirect()->route('admin.lines.index')
                ->with('error', 'Nie można usunąć linii, która ma przypisane pojazdy.');
        }

        $line->delete();
        return redirect()->route('admin.lines.index')
            ->with('success', 'Linia została usunięta pomyślnie.');
    }
}
