<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Departure;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index()
    {
        $tickets = Ticket::with(['user', 'departure.schedule.route.line'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $departures = Departure::with(['schedule.route.line'])
            ->whereDate('departure_time', '>=', now())
            ->orderBy('departure_time')
            ->get();
            
        return view('admin.tickets.create', compact('users', 'departures'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'departure_id' => 'required|exists:departures,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:reserved,paid,used,cancelled',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }
        
        // Generate a random ticket number if not provided
        if (!isset($data['ticket_number'])) {
            $data['ticket_number'] = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));
        }
        
        // Set purchase date if not provided
        if (!isset($data['purchase_date'])) {
            $data['purchase_date'] = now();
        }

        Ticket::create($data);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Bilet został dodany pomyślnie.');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Rozszerzone ładowanie relacji z jawnym załadowaniem modelu Vehicle
        $ticket->load([
            'user', 
            'departure.schedule.route.line', 
            'departure.vehicle'
        ]);
        
        // Jeśli departure istnieje, upewnij się, że vehicle jest załadowany
        if ($ticket->departure && $ticket->departure->vehicle_id) {
            $ticket->departure->load('vehicle');
        }
        
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Ticket $ticket)
    {
        $users = User::orderBy('name')->get();
        $departures = Departure::with(['schedule.route.line'])
            ->orderBy('departure_time')
            ->get();
            
        return view('admin.tickets.edit', compact('ticket', 'users', 'departures'));
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'departure_id' => 'required|exists:departures,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:reserved,paid,used,cancelled',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        $ticket->update($data);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Bilet został zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified ticket from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Add ticket cancellation logic
        
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Bilet został usunięty pomyślnie.');
    }
}
