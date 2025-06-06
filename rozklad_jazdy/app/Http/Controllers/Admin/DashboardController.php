<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Line;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // CheckRole middleware applied in routes
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::count();
        $carriers = Carrier::count();
        $lines = Line::count();

        return view('admin.dashboard', compact('users', 'carriers', 'lines'));
    }
}
