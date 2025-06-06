<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;

class AdminCarrierController extends Controller
{
    /**
     * Display a listing of the carriers.
     */
    public function index()
    {
        $carriers = Carrier::orderBy('name')->paginate(10);
        return view('admin.carriers.index', compact('carriers'));
    }

    /**
     * Show the form for creating a new carrier.
     */
    public function create()
    {
        return view('admin.carriers.create');
    }

    /**
     * Store a newly created carrier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'required|url|max:255',
        ]);

        Carrier::create($request->all());

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Przewoźnik został dodany pomyślnie.');
    }

    /**
     * Display the specified carrier.
     */
    public function show(Carrier $carrier)
    {
        return view('admin.carriers.show', compact('carrier'));
    }

    /**
     * Show the form for editing the specified carrier.
     */
    public function edit(Carrier $carrier)
    {
        return view('admin.carriers.edit', compact('carrier'));
    }

    /**
     * Update the specified carrier in storage.
     */
    public function update(Request $request, Carrier $carrier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'required|url|max:255',
        ]);

        $carrier->update($request->all());

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Przewoźnik został zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified carrier from storage.
     */
    public function destroy(Carrier $carrier)
    {
        // Check if the carrier has any associated lines
        if ($carrier->lines()->count() > 0) {
            return redirect()->route('admin.carriers.index')
                ->with('error', 'Nie można usunąć przewoźnika, który ma przypisane linie.');
        }

        $carrier->delete();

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Przewoźnik został usunięty pomyślnie.');
    }
}
