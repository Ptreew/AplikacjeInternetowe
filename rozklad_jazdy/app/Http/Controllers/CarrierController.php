<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CarrierController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods except index and show
        $this->middleware('auth')->except(['index', 'show']);
        
        // Require admin role for create, store, edit, update, destroy
        $this->middleware('role:admin')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = Carrier::query();
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        // Paginate the results
        $carriers = $query->orderBy('name')->paginate(10);
        
        return view('carriers.index', compact('carriers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('carriers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:carriers,email|max:255',
            'website' => 'nullable|url|max:255',
        ]);
        
        // Create new carrier
        $carrier = new Carrier();
        $carrier->name = $validated['name'];
        $carrier->email = $validated['email'];
        $carrier->website = $validated['website'] ?? null;
        $carrier->save();
        
        return redirect()->route('carriers.show', $carrier)
            ->with('success', 'Carrier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrier $carrier)
    {
        // Load related lines with their routes
        $carrier->load('lines');
        
        return view('carriers.show', compact('carrier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrier $carrier)
    {
        return view('carriers.edit', compact('carrier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrier $carrier)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('carriers')->ignore($carrier->id),
                'max:255',
            ],
            'website' => 'nullable|url|max:255',
        ]);
        
        // Update carrier
        $carrier->name = $validated['name'];
        $carrier->email = $validated['email'];
        $carrier->website = $validated['website'] ?? null;
        $carrier->save();
        
        return redirect()->route('carriers.show', $carrier)
            ->with('success', 'Carrier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrier $carrier)
    {
        // Check if the carrier has any related lines
        if ($carrier->lines()->exists()) {
            return redirect()->route('carriers.show', $carrier)
                ->with('error', 'Cannot delete carrier with associated lines. Please delete the lines first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the carrier
            $carrier->delete();
            
            DB::commit();
            
            return redirect()->route('carriers.index')
                ->with('success', 'Carrier deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('carriers.show', $carrier)
                ->with('error', 'Failed to delete carrier: ' . $e->getMessage());
        }
    }
}
