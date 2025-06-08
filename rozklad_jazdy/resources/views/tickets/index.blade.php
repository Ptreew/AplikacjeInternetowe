@extends('layouts.app')

@section('title', 'Moje bilety')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Moje bilety</h4>
                    <a href="{{ route('routes.search') }}" class="btn btn-success">
                        <i class="fas fa-search me-1"></i> Znajdź połączenie
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if($tickets->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                            <h5>Nie masz jeszcze żadnych biletów</h5>
                            <p class="text-muted">Skorzystaj z wyszukiwarki, aby znaleźć i zarezerwować bilet.</p>
                            <a href="{{ route('routes.search') }}" class="btn btn-success mt-3">
                                <i class="fas fa-search me-1"></i> Znajdź połączenie
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Numer biletu</th>
                                        <th>Data przejazdu</th>
                                        <th>Trasa</th>
                                        <th>Status</th>
                                        <th class="text-end">Cena</th>
                                        <th class="text-end">Akcje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        @php
                                            $departure = $ticket->departure;
                                            $route = $departure->schedule->route;
                                            $firstStop = $route->stops->first();
                                            $lastStop = $route->stops->last();
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $ticket->ticket_number }}</strong>
                                                <div class="text-muted small">
                                                    {{ $ticket->purchase_date->format('d.m.Y H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($ticket->travel_date . ' ' . $departure->departure_time)->format('d.m.Y H:i') }}
                                                <div class="text-muted small">
                                                    {{ $departure->vehicle->type ?? 'Brak danych' }}
                                                    @if(!empty($departure->vehicle->vehicle_number))
                                                        (nr {{ $departure->vehicle->vehicle_number }})
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $route->line->name }}</div>
                                                <div class="text-muted small">
                                                    {{ $firstStop->city->name }} → {{ $lastStop->city->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $ticket->status === 'paid' ? 'success' : 
                                                    ($ticket->status === 'reserved' ? 'warning' : 
                                                    ($ticket->status === 'used' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ 
                                                        $ticket->status === 'paid' ? 'Opłacony' : 
                                                        ($ticket->status === 'reserved' ? 'Zarezerwowany' : 
                                                        ($ticket->status === 'used' ? 'Wykorzystany' : 'Anulowany')) 
                                                    }}
                                                </span>
                                                @if(!$ticket->is_active)
                                                    <span class="badge bg-secondary ms-1">Nieaktywny</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-nowrap">
                                                {{ number_format($departure->price, 2) }} zł
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-eye"></i> Szczegóły
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 1rem 1.5rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        white-space: nowrap;
    }
    
    .badge.bg-warning {
        color: #000;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    
    .table-hover > tbody > tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endsection
