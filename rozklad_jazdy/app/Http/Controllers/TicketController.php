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
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // For admin users, show all tickets with pagination and filters
        if ($user && $user->role === 'Admin') {
            $query = Ticket::with(['user', 'departure.schedule.route', 'departure.vehicle'])
                ->orderBy('created_at', 'desc');
                
            // Apply filters if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('date_from')) {
                $query->whereHas('departure', function($q) use ($request) {
                    $q->whereDate('departure_time', '>=', $request->date_from);
                });
            }
            
            if ($request->has('date_to')) {
                $query->whereHas('departure', function($q) use ($request) {
                    $q->whereDate('departure_time', '<=', $request->date_to);
                });
            }
            
            $tickets = $query->paginate(15);
            return view('tickets.index', compact('tickets'));
        }
        
        // For regular users, show only their tickets
        $tickets = Ticket::with(['departure.schedule.route', 'departure.vehicle'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // If departure_id is provided, we're buying a ticket for a specific departure
        if ($request->has('departure_id')) {
            $departure = Departure::with(['schedule.route.routeStops.stop.city', 'vehicle'])
                ->findOrFail($request->departure_id);
                
            return view('tickets.create', compact('departure'));
        }
        
        // Otherwise show a form to search for departures first
        return view('tickets.search');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'departure_id' => 'required|exists:departures,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if the departure exists and is active
        $departure = Departure::findOrFail($request->departure_id);
        if (!$departure->is_active) {
            return redirect()->back()->with('error', 'This departure is not available for booking.');
        }
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Create the ticket
            $ticket = new Ticket();
            $ticket->user_id = Auth::id();
            $ticket->departure_id = $request->departure_id;
            $ticket->ticket_number = 'TKT-' . Str::upper(Str::random(8));
            $ticket->price = 25.00; // In a real app, this would be calculated based on the route, etc.
            $ticket->status = 'reserved';
            $ticket->passenger_name = $request->passenger_name;
            $ticket->passenger_email = $request->passenger_email;
            $ticket->passenger_phone = $request->passenger_phone;
            $ticket->notes = $request->notes;
            $ticket->save();
            
            DB::commit();
            
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket has been reserved successfully. Please complete payment to confirm.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reserve ticket: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // Make sure users can only see their own tickets unless they're admin
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'Admin') {
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
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'Admin') {
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
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'Admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to update this ticket.');
        }
        
        // Can't update tickets that are already used or cancelled
        if (in_array($ticket->status, ['used', 'cancelled'])) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket cannot be updated because it has been ' . $ticket->status . '.');
        }
        
        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Only admins can change the status
        if (Auth::user()->role === 'Admin' && $request->has('status')) {
            $request->validate([
                'status' => 'required|in:reserved,paid,cancelled,used',
            ]);
            $ticket->status = $request->status;
        }
        
        $ticket->passenger_name = $request->passenger_name;
        $ticket->passenger_email = $request->passenger_email;
        $ticket->passenger_phone = $request->passenger_phone;
        $ticket->notes = $request->notes;
        $ticket->save();
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket details have been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Only admins or ticket owners can delete tickets
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'Admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'You are not authorized to delete this ticket.');
        }
        
        // Can't delete tickets that are already used
        if ($ticket->status === 'used') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket cannot be deleted because it has been used.');
        }
        
        // For paid tickets, set status to cancelled instead of deleting
        if ($ticket->status === 'paid') {
            $ticket->status = 'cancelled';
            $ticket->save();
            return redirect()->route('tickets.index')
                ->with('success', 'Ticket has been cancelled successfully.');
        }
        
        // For reserved tickets, actually delete them
        $ticket->delete();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket has been deleted successfully.');
    }
    
    /**
     * Search for available departures to purchase tickets
     */
    public function search(Request $request)
    {
        $request->validate([
            'from_city' => 'required|exists:cities,id',
            'to_city' => 'required|exists:cities,id|different:from_city',
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $dayOfWeek = Carbon::parse($request->date)->dayOfWeek;
        
        // Find routes that connect the selected cities
        $departures = Departure::with(['schedule.route.routeStops.stop.city', 'vehicle'])
            ->whereHas('schedule', function($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                      ->where('is_active', true);
            })
            ->whereHas('schedule.route', function($query) use ($request) {
                $query->where('is_active', true)
                      ->whereHas('routeStops', function($q1) use ($request) {
                          $q1->whereHas('stop', function($q2) use ($request) {
                              $q2->where('city_id', $request->from_city);
                          });
                      })
                      ->whereHas('routeStops', function($q1) use ($request) {
                          $q1->whereHas('stop', function($q2) use ($request) {
                              $q2->where('city_id', $request->to_city);
                          });
                      });
            })
            ->where('is_active', true)
            ->whereDate('departure_time', $date)
            ->orderBy('departure_time')
            ->paginate(15);
        
        return view('tickets.search_results', compact('departures', 'request'));
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
        
        // In a real app, you would integrate with a payment gateway here
        // For now, we'll just simulate successful payment
        
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
        if (Auth::user()->role !== 'Admin') {
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
