<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Stop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get all cities for the dropdown menus
        $cities = City::orderBy('name')->get();
        
        // Get all stops for the city search form
        $stops = Stop::orderBy('name')->get();
        
        // Get unique vehicle types for the dropdown menu
        $vehicleTypes = DB::table('vehicles')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
            
        return view('index', compact('cities', 'stops', 'vehicleTypes'));
    }
}
