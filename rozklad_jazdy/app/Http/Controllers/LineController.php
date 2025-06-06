<?php

namespace App\Http\Controllers;

use App\Models\Line;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Middleware\CheckRole;

class LineController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods except index and show
        $this->middleware('auth')->except(['index', 'show']);
        
        // Require admin role for create, store, edit, update, destroy
        $this->middleware(CheckRole::class . ':admin')->except(['index', 'show', 'favorites', 'toggleFavorite']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add search filters if provided
        $query = Line::with('carrier');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            // Search for lines by name or carrier name
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('carrier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('carrier_id') && !empty($request->carrier_id)) {
            $query->where('carrier_id', $request->carrier_id);
        }
        
        // Paginate the results
        $lines = $query->orderBy('carrier_id')->orderBy('name')->paginate(15);
        
        // Get carriers for filter dropdown
        $carriers = Carrier::orderBy('name')->get();
        
        return view('lines.index', compact('lines', 'carriers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('lines.create', compact('carriers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lines')->where(function ($query) use ($request) {
                    return $query->where('carrier_id', $request->carrier_id);
                }),
            ],
            'type' => 'required|string|in:train,bus,tram,metro,ferry',
            'number' => 'required|string|max:10',
        ]);
        
        // Create new line
        $line = new Line();
        $line->carrier_id = $validated['carrier_id'];
        $line->name = $validated['name'];
        $line->type = $validated['type'];
        $line->number = $validated['number'];
        $line->save();
        
        return redirect()->route('lines.show', $line)
            ->with('success', 'Line created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Line $line)
    {
        // Load related routes
        $line->load(['carrier', 'routes']);
        
        // Check if the user has favorited this line
        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = Auth::user()->favouriteLines()->where('line_id', $line->id)->exists();
        }
        
        return view('lines.show', compact('line', 'isFavorite'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Line $line)
    {
        $carriers = Carrier::orderBy('name')->get();
        return view('lines.edit', compact('line', 'carriers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
        // Validate the request data
        $validated = $request->validate([
            'carrier_id' => 'required|exists:carriers,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lines')->where(function ($query) use ($request) {
                    return $query->where('carrier_id', $request->carrier_id);
                })->ignore($line->id),
            ],
            'type' => 'required|string|in:train,bus,tram,metro,ferry',
            'number' => 'required|string|max:10',
        ]);
        
        // Update line
        $line->carrier_id = $validated['carrier_id'];
        $line->name = $validated['name'];
        $line->type = $validated['type'];
        $line->number = $validated['number'];
        $line->save();
        
        return redirect()->route('lines.show', $line)
            ->with('success', 'Line updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
        // Check if the line has any routes
        if ($line->routes()->exists()) {
            return redirect()->route('lines.show', $line)
                ->with('error', 'Cannot delete line with associated routes. Please delete the routes first.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete favorites first
            DB::table('favourite_lines')->where('line_id', $line->id)->delete();
            
            // Delete the line
            $line->delete();
            
            DB::commit();
            
            return redirect()->route('lines.index')
                ->with('success', 'Line deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('lines.show', $line)
                ->with('error', 'Failed to delete line: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle favorite status for a line
     */
    public function toggleFavorite(Line $line)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check if user has already favorited this line
        $favorite = DB::table('favourite_lines')
            ->where('user_id', $user->id)
            ->where('line_id', $line->id)
            ->first();
            
        if ($favorite) {
            // Remove from favorites
            DB::table('favourite_lines')
                ->where('user_id', $user->id)
                ->where('line_id', $line->id)
                ->delete();
                
            $message = 'Line removed from favorites.';
        } else {
            // Add to favorites
            DB::table('favourite_lines')->insert([
                'user_id' => $user->id,
                'line_id' => $line->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $message = 'Line added to favorites.';
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Display the user's favorite lines
     */
    public function favorites()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $lines = Auth::user()->favouriteLines()->with('carrier')->paginate(10);
        
        return view('lines.favorites', compact('lines'));
    }
}
