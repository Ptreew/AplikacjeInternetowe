<?php

namespace App\Observers;

use App\Models\Ticket;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Jeśli bilet jest aktywny, zmniejsz liczbę dostępnych miejsc
        if ($ticket->status === 'active' || $ticket->is_active) {
            $departure = $ticket->departure;
            
            // Jeśli available_seats jest null, zainicjuj na podstawie pojazdu
            if ($departure->available_seats === null) {
                $departure->available_seats = $departure->vehicle->capacity ?? 0;
            }
            
            // Zmniejsz liczbę miejsc tylko jeśli jest większa od 0
            if ($departure->available_seats > 0) {
                $departure->decrement('available_seats');
            }
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Jeśli status biletu zmienił się z aktywnego na anulowany
        if ($ticket->isDirty('status') && 
            $ticket->getOriginal('status') === 'active' && 
            $ticket->status === 'cancelled') {
            
            // Zwiększ liczbę dostępnych miejsc
            $ticket->departure->increment('available_seats');
        }
        
        // Jeśli status biletu zmienił się z anulowanego na aktywny
        if ($ticket->isDirty('status') && 
            $ticket->getOriginal('status') === 'cancelled' && 
            $ticket->status === 'active') {
            
            // Zmniejsz liczbę dostępnych miejsc
            if ($ticket->departure->available_seats > 0) {
                $ticket->departure->decrement('available_seats');
            }
        }
        
        // Jeśli pole is_active się zmieniło
        if ($ticket->isDirty('is_active')) {
            if ($ticket->is_active) {
                // Aktywacja biletu - zmniejsz dostępne miejsca
                if ($ticket->departure->available_seats > 0) {
                    $ticket->departure->decrement('available_seats');
                }
            } else {
                // Dezaktywacja biletu - zwiększ dostępne miejsca
                $ticket->departure->increment('available_seats');
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Jeśli usuwamy aktywny bilet, zwiększ liczbę miejsc
        if (($ticket->status === 'active' || $ticket->is_active) && $ticket->departure) {
            $ticket->departure->increment('available_seats');
        }
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
