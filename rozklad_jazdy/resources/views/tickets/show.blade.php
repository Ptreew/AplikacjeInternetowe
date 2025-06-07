@extends('layouts.app')

@section('title', 'Szczegóły biletu')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Szczegóły biletu</h4>
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
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h5>Dane biletu:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-25">Numer biletu</th>
                                        <td>{{ $ticket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Data zakupu</th>
                                        <td>{{ $ticket->purchase_date->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
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
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Szczegóły przejazdu:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-25">Linia</th>
                                        <td>{{ $ticket->departure->schedule->route->line->name ?? 'Brak danych' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Przewoźnik</th>
                                        <td>{{ $ticket->departure->schedule->route->line->carrier->name ?? 'Brak danych' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Trasa</th>
                                        <td>
                                            @php
                                                $stops = $ticket->departure->schedule->route->stops;
                                                $firstStop = $stops->first();
                                                $lastStop = $stops->last();
                                            @endphp
                                            {{ $firstStop->city->name ?? 'Brak danych' }} ({{ $firstStop->name }}) → 
                                            {{ $lastStop->city->name ?? 'Brak danych' }} ({{ $lastStop->name }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Data i godzina odjazdu</th>
                                        <td>{{ \Carbon\Carbon::parse($ticket->travel_date . ' ' . $ticket->departure->departure_time)->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cena</th>
                                        <td class="h5 text-primary">{{ number_format($ticket->departure->price, 2) }} zł</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pojazd</th>
                                        <td>
                                            {{ $ticket->departure->vehicle->type ?? 'Brak danych' }}
                                            @if(!empty($ticket->departure->vehicle->vehicle_number))
                                                (nr {{ $ticket->departure->vehicle->vehicle_number }})
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Dane pasażera:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-25">Imię i nazwisko</th>
                                        <td>{{ $ticket->passenger_name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Adres e-mail</th>
                                        <td>{{ $ticket->passenger_email }}</td>
                                    </tr>
                                    @if($ticket->passenger_phone)
                                    <tr>
                                        <th scope="row">Telefon kontaktowy</th>
                                        <td>{{ $ticket->passenger_phone }}</td>
                                    </tr>
                                    @endif
                                    @if($ticket->notes)
                                    <tr>
                                        <th scope="row">Uwagi</th>
                                        <td>{{ $ticket->notes }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tickets.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Powrót do listy biletów
                        </a>
                        
                        <div class="btn-group">
                            @if($ticket->status === 'reserved' || $ticket->status === 'paid')
                                @if($ticket->status === 'reserved')
                                    <form action="{{ route('tickets.pay', $ticket) }}" method="POST" class="me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-credit-card me-1"></i> Opłać bilet
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('tickets.cancel', $ticket) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Czy na pewno chcesz anulować ten bilet?')">
                                        <i class="fas fa-times me-1"></i> Anuluj bilet
                                    </button>
                                </form>
                            @endif
                            
                            @if(auth()->user()->role === 'admin' && $ticket->status === 'paid')
                                <form action="{{ route('tickets.mark-used', $ticket) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Oznacz jako wykorzystany
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
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
    
    .card-body {
        padding: 2rem;
    }
    
    .table th {
        background-color: #f8f9fa;
    }
    
    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
    
    .badge.bg-warning {
        color: #000;
    }
</style>
@endsection
