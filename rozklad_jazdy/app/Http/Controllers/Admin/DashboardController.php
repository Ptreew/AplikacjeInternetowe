<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Line;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        // Podstawowe statystyki
        $users = User::count();
        $carriers = Carrier::count();
        $lines = Line::count();
        $vehicles = Vehicle::count();
        $stops = Stop::count();
        
        // Statystyki tras
        $cityRoutes = Route::where('type', 'city')->count();
        $intercityRoutes = Route::where('type', 'intercity')->count();
        
        // Statystyki aktywności
        $today = Carbon::today();
        $loginsToday = 0; // to wymaga tabeli z logami logowania
        $searchesToday = 0; // to wymaga tabeli z logami wyszukiwań
        
        // Ostatnie logowania - przykładowe dane
        $recentLogins = [
            ['user' => 'jan.kowalski@example.com', 'time' => Carbon::now()->subHours(1)->format('Y-m-d H:i'), 'status' => 'Sukces', 'ip' => '192.168.1.1'],
            ['user' => 'anna.nowak@example.com', 'time' => Carbon::now()->subHours(2)->format('Y-m-d H:i'), 'status' => 'Sukces', 'ip' => '192.168.1.5'],
            ['user' => 'test@example.com', 'time' => Carbon::now()->subHours(3)->format('Y-m-d H:i'), 'status' => 'Błąd', 'ip' => '192.168.1.10']
        ];
        
        // Statystyki sprzedaży
        $ticketsSold = Ticket::count();
        $ticketsDaily = Ticket::whereDate('created_at', $today)->count();
        
        // Obliczenie przychodu - cena biletu jest pobierana z powiązanej tabeli departures
        // pobieramy wszystkie bilety z relacją departure
        $tickets = Ticket::with('departure')->get();
        $revenue = 0;
        foreach ($tickets as $ticket) {
            if ($ticket->departure && isset($ticket->departure->price)) {
                $revenue += $ticket->departure->price;
            }
        }
        
        // Popularność tras (przykładowe dane)
        $popularRoutes = [
            ['route' => 'Kraków - Warszawa', 'searches' => 1245, 'percent' => 85],
            ['route' => 'Kraków - Wrocław', 'searches' => 980, 'percent' => 67],
            ['route' => 'Warszawa - Gdańsk', 'searches' => 875, 'percent' => 60],
            ['route' => 'Kraków - Zakopane', 'searches' => 750, 'percent' => 51],
            ['route' => 'Warszawa - Poznań', 'searches' => 680, 'percent' => 47]
        ];
        
        // Obciążenie systemu (przykładowe dane)
        $cpuUsage = 25; // w procentach
        $memoryUsage = 42; // w procentach
        $diskUsage = 36; // w procentach
        
        // Ostatnie błędy systemu (przykładowe dane)
        $systemErrors = [
            ['time' => Carbon::now()->subMinutes(30)->format('Y-m-d H:i'), 'code' => 500, 'message' => 'Internal Server Error', 'source' => 'AdminController.php'],
            ['time' => Carbon::now()->subHours(2)->format('Y-m-d H:i'), 'code' => 404, 'message' => 'Page not found', 'source' => 'RouteController.php'],
            ['time' => Carbon::now()->subDays(1)->format('Y-m-d H:i'), 'code' => 403, 'message' => 'Forbidden', 'source' => 'AuthController.php']
        ];

        return view('admin.dashboard', compact(
            'users', 'carriers', 'lines', 'vehicles', 'stops', 
            'cityRoutes', 'intercityRoutes', 
            'loginsToday', 'searchesToday', 'recentLogins',
            'ticketsSold', 'ticketsDaily', 'revenue', 'popularRoutes',
            'cpuUsage', 'memoryUsage', 'diskUsage', 'systemErrors'
        ));
    }
}
