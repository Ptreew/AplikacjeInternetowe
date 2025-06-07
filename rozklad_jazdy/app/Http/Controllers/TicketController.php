<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Departure;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Show only logged-in user's tickets for all users
        $tickets = Ticket::with(['departure.schedule.route', 'departure.vehicle'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new ticket
     */
    public function create(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Musisz być zalogowany, aby zarezerwować bilet.');
        }

        // If departure_id is provided, we're buying a ticket for a specific departure
        if ($request->has('departure_id')) {
            $departure = Departure::with([
                'schedule.route.line.carrier',
                'schedule.route.stops.city',
                'vehicle'
            ])->findOrFail($request->departure_id);
            
            // Determine selected travel date (defaults to today if not provided)
            $travelDate = $request->input('travel_date');
            if ($travelDate) {
                try {
                    $travelDateCarbon = Carbon::parse($travelDate)->startOfDay();
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Nieprawidłowa data podróży.');
                }
            } else {
                $travelDateCarbon = Carbon::today();
            }
            
            // Check if departure is active
            if (!$departure->is_active) {
                return redirect()->back()->with('error', 'To połączenie nie jest obecnie dostępne.');
            }
            
            // Combine travel date with departure time to get full DateTime
            $departureDateTime = Carbon::parse($travelDateCarbon->format('Y-m-d').' '.$departure->departure_time);
            
            // Check if departure datetime is in the past
            if ($departureDateTime->lt(Carbon::now())) {
                return redirect()->back()->with('error', 'Nie można kupić biletu - połączenie już odjechało. Prosimy wybrać inny termin.');
            }
            
            return view('tickets.create', [
                'departure' => $departure,
                'travelDate' => $travelDateCarbon->format('Y-m-d')
            ]);
        }
        
        // Redirect to search if no departure_id is provided
        return redirect()->route('routes.search');
    }

    /**
     * Store a newly created ticket in storage
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'departure_id' => 'required|exists:departures,id',
            'travel_date'  => 'required|date|after_or_equal:today',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20|regex:/^[+]?[0-9]+$/',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Musisz być zalogowany, aby zarezerwować bilet.');
        }
        
        // Check if the departure exists and is active
        $departure = Departure::with('schedule.route.line.carrier')
            ->findOrFail($request->departure_id);
            
        if (!$departure->is_active) {
            return redirect()->back()->with('error', 'To połączenie nie jest obecnie dostępne.');
        }
        
        // Determine selected travel date
        $travelDateCarbon = Carbon::parse($validated['travel_date'])->startOfDay();
        
        // Combine travel date with departure time to get full DateTime
        $departureDateTime = Carbon::parse($travelDateCarbon->format('Y-m-d').' '.$departure->departure_time);
        
        // Check if departure datetime is in the past
        if ($departureDateTime->lt(Carbon::now())) {
            return redirect()->back()->with('error', 'Nie można kupić biletu - połączenie już odjechało. Prosimy wybrać inny termin.');
        }
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Generate unique ticket number
            $ticketNumber = 'TKT-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            
            // Create the ticket
            $ticket = new Ticket();
            $ticket->user_id = Auth::id();
            $ticket->departure_id = $request->departure_id;
            $ticket->ticket_number = $ticketNumber;
            $ticket->status = 'reserved';
            $ticket->purchase_date = now();
            $ticket->passenger_name = $request->passenger_name;
            $ticket->passenger_email = $request->passenger_email;
            $ticket->passenger_phone = $request->passenger_phone;
            $ticket->notes = $request->notes;
            $ticket->is_active = true;
            
            // Always save travel date
            $ticket->travel_date = $travelDateCarbon->format('Y-m-d');
            
            $ticket->save();
            
            DB::commit();
            
            // Redirect to ticket details with success message
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Bilet został pomyślnie zarezerwowany. Możesz go teraz opłacić.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Błąd podczas rezerwacji biletu: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas rezerwacji biletu. Prosimy spróbować ponownie.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // Make sure users can only see their own tickets unless they're admin
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to view this ticket.');
        }
        
        $ticket->load(['departure.schedule.route.routeStops.stop.city', 'departure.vehicle']);
        
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        // Only admins or ticket owners can edit tickets
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to edit this ticket.');
        }
        
        // Can't edit tickets that are already used or cancelled
        if (in_array($ticket->status, ['used', 'cancelled'])) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket cannot be edited because it has been ' . $ticket->status . '.');
        }
        
        $ticket->load('departure.schedule.route');
        
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        // Only admins or ticket owners can update tickets
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'Nie masz uprawnień do edycji tego biletu.');
        }
        
        // Can't update tickets that are already used or cancelled
        if (in_array($ticket->status, ['used', 'cancelled'])) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Nie można edytować biletu, który został ' . ($ticket->status === 'used' ? 'wykorzystany' : 'anulowany') . '.');
        }
        
        // For normal users, only allow status changes (cancel tickets)
        if (Auth::user()->role !== 'admin') {
            $request->validate([
                'status' => 'required|in:cancelled',
            ]);
            
            // Can't cancel paid tickets
            if ($ticket->status === 'paid') {
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'Nie można anulować opłaconego biletu. Skontaktuj się z obsługą klienta.');
            }
            
            $ticket->status = 'cancelled';
            $ticket->is_active = false;
            $ticket->save();
            
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Bilet został anulowany.');
        }
        
        // For admins, allow full edits
        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:reserved,paid,used,cancelled',
        ]);
        
        if ($request->has('status')) {
            $ticket->status = $request->status;
            
            // Deactivate ticket if it's used or cancelled
            if (in_array($request->status, ['used', 'cancelled'])) {
                $ticket->is_active = false;
            }
        }
        
        $ticket->passenger_name = $request->passenger_name;
        $ticket->passenger_email = $request->passenger_email;
        $ticket->passenger_phone = $request->passenger_phone;
        $ticket->notes = $request->notes;
        $ticket->save();
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Dane biletu zostały zaktualizowane pomyślnie.');
    }
    
    /**
     * Cancel the specified ticket.
     */
    public function destroy(Ticket $ticket)
    {
        // Check if user is authorized to cancel this ticket
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'Nie masz uprawnień do anulowania tego biletu.');
        }
        
        // Check if ticket can be cancelled
        if ($ticket->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Ten bilet został już wcześniej anulowany.');
        }
        
        if ($ticket->status === 'used') {
            return redirect()->back()
                ->with('error', 'Nie można anulować już wykorzystanego biletu.');
        }
        
        if ($ticket->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Nie można anulować opłaconego biletu. Skontaktuj się z obsługą klienta.');
        }
        
        // Update ticket status to cancelled
        $ticket->update([
            'status' => 'cancelled',
            'is_active' => false
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Bilet został pomyślnie anulowany.');
    }
    
    /**
     * Search for available departures to purchase tickets
     */
    public function search(Request $request)
    {
        $request->validate([
            'from_city' => 'required_without:from_stop|exists:cities,id',
            'to_city' => 'required_without:to_stop|exists:cities,id',
            'from_stop' => 'required_without:from_city|exists:stops,id',
            'to_stop' => 'required_without:to_city|exists:stops,id',
            'date' => 'required|date',
            'time_from' => 'nullable|date_format:H:i',
            'time_to' => 'nullable|date_format:H:i|after_or_equal:time_from',
        ]);
        
        $query = Departure::with([
            'schedule.route.line.carrier',
            'schedule.route.stops.city',
            'vehicle'
        ])
        ->where('is_active', true)
        ->whereDate('departure_time', '>=', now())
        ->orderBy('departure_time');
        
        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }
        
        // Filter by time range
        if ($request->filled('time_from')) {
            $query->whereTime('departure_time', '>=', $request->time_from);
        }
        
        if ($request->filled('time_to')) {
            $query->whereTime('departure_time', '<=', $request->time_to);
        }
        
        // Filter by from city/stop
        if ($request->filled('from_city')) {
            $query->whereHas('schedule.route.stops', function($q) use ($request) {
                $q->where('city_id', $request->from_city);
            });
        } elseif ($request->filled('from_stop')) {
            $query->whereHas('schedule.route.stops', function($q) use ($request) {
                $q->where('stop_id', $request->from_stop);
            });
        }
        
        // Filter by to city/stop
        if ($request->filled('to_city')) {
            $query->whereHas('schedule.route.stops', function($q) use ($request) {
                $q->where('city_id', $request->to_city);
            });
        } elseif ($request->filled('to_stop')) {
            $query->whereHas('schedule.route.stops', function($q) use ($request) {
                $q->where('stop_id', $request->to_stop);
            });
        }
        
        $departures = $query->paginate(10);
        
        // Get cities and stops for the search form
        $cities = \App\Models\City::orderBy('name')->get();
        $stops = \App\Models\Stop::with('city')->orderBy('name')->get();
        
        return view('tickets.search-results', [
            'departures' => $departures,
            'cities' => $cities,
            'stops' => $stops,
            'searchParams' => $request->all()
        ]);
    }
    
    /**
     * Cancel a ticket
     */
    public function cancel(Ticket $ticket)
    {
        // Only ticket owner can cancel their ticket
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'Nie masz uprawnień do anulowania tego biletu.');
        }
        
        // Check if ticket can be cancelled
        if ($ticket->status === 'cancelled') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Ten bilet został już wcześniej anulowany.');
        }
        
        if ($ticket->status === 'used') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Nie można anulować już wykorzystanego biletu.');
        }
        
        if ($ticket->status === 'paid') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Nie można anulować opłaconego biletu. Skontaktuj się z obsługą klienta.');
        }
        
        // Update ticket status to cancelled
        $ticket->status = 'cancelled';
        $ticket->is_active = false;
        $ticket->save();
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Bilet został pomyślnie anulowany.');
    }

    /**
     * Process payment for a reserved ticket
     */
    public function pay(Request $request, Ticket $ticket)
    {
        // Only ticket owner can pay for it
        if (Auth::id() !== $ticket->user_id) {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to pay for this ticket.');
        }
        
        // Can only pay for reserved tickets
        if ($ticket->status !== 'reserved') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket is not available for payment.');
        }
        
        // simulate payment
        
        $ticket->status = 'paid';
        $ticket->purchase_date = now();
        $ticket->save();
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Payment successful! Your ticket has been confirmed.');
    }
    
    /**
     * Mark a ticket as used (admin only)
     */
    public function markAsUsed(Ticket $ticket)
    {
        // Only admins can mark tickets as used
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to perform this action.');
        }
        
        // Can only mark paid tickets as used
        if ($ticket->status !== 'paid') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Only paid tickets can be marked as used.');
        }
        
        $ticket->status = 'used';
        $ticket->save();
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket has been marked as used.');
    }
}
