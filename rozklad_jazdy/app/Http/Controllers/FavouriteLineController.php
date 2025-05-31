<?php

namespace App\Http\Controllers;

use App\Models\FavouriteLine;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavouriteLineController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Require authentication for all methods
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the authenticated user's favorite lines
        $favouriteLines = FavouriteLine::with(['line.carrier', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('favourite_lines.index', compact('favouriteLines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all available lines that the user hasn't favorited yet
        $userId = Auth::id();
        $favouritedLineIds = FavouriteLine::where('user_id', $userId)->pluck('line_id');
        
        $availableLines = Line::with('carrier')
            ->whereNotIn('id', $favouritedLineIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('favourite_lines.create', compact('availableLines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => [
                'required',
                'exists:lines,id',
                function ($attribute, $value, $fail) {
                    // Check if the user already has this line as a favorite
                    $exists = FavouriteLine::where('user_id', Auth::id())
                        ->where('line_id', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('You have already added this line to your favorites.');
                    }
                },
            ],
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create new favorite line
            $favouriteLine = new FavouriteLine();
            $favouriteLine->user_id = Auth::id();
            $favouriteLine->line_id = $validated['line_id'];
            $favouriteLine->save();
            
            DB::commit();
            
            return redirect()->route('favourite-lines.index')
                ->with('success', 'Line added to favorites successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to add line to favorites: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FavouriteLine $favouriteLine)
    {
        // Check if the favorite line belongs to the authenticated user
        if ($favouriteLine->user_id !== Auth::id()) {
            return redirect()->route('favourite-lines.index')
                ->with('error', 'You do not have permission to view this favorite line.');
        }
        
        // Load related data
        $favouriteLine->load(['line.carrier', 'line.routes.routeStops.stop.city', 'user']);
        
        return view('favourite_lines.show', compact('favouriteLine'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FavouriteLine $favouriteLine)
    {
        // Check if the favorite line belongs to the authenticated user
        if ($favouriteLine->user_id !== Auth::id()) {
            return redirect()->route('favourite-lines.index')
                ->with('error', 'You do not have permission to remove this favorite line.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete the favorite line
            $favouriteLine->delete();
            
            DB::commit();
            
            return redirect()->route('favourite-lines.index')
                ->with('success', 'Line removed from favorites successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('favourite-lines.index')
                ->with('error', 'Failed to remove line from favorites: ' . $e->getMessage());
        }
    }
    
    /**
     * Add a line to favorites via AJAX.
     */
    public function addToFavorites(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => [
                'required',
                'exists:lines,id',
                function ($attribute, $value, $fail) {
                    // Check if the user already has this line as a favorite
                    $exists = FavouriteLine::where('user_id', Auth::id())
                        ->where('line_id', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('You have already added this line to your favorites.');
                    }
                },
            ],
        ]);
        
        try {
            // Create new favorite line
            $favouriteLine = new FavouriteLine();
            $favouriteLine->user_id = Auth::id();
            $favouriteLine->line_id = $validated['line_id'];
            $favouriteLine->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Line added to favorites successfully.',
                'favourite_id' => $favouriteLine->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add line to favorites: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove a line from favorites via AJAX.
     */
    public function removeFromFavorites(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'line_id' => 'required|exists:lines,id',
        ]);
        
        try {
            // Find and delete the favorite line
            $favouriteLine = FavouriteLine::where('user_id', Auth::id())
                ->where('line_id', $validated['line_id'])
                ->firstOrFail();
            
            $favouriteLine->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Line removed from favorites successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove line from favorites: ' . $e->getMessage()
            ], 500);
        }
    }
}
