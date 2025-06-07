@extends('layouts.app')

@section('title', 'Szczegóły odjazdu')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap gap-2">
            <a href="{{ route('admin.schedules.show', $departure->schedule_id) }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Powrót do rozkładu
            </a>
            <a href="{{ route('admin.departures.edit', $departure) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edytuj odjazd
            </a>
            <form action="{{ route('admin.departures.destroy', $departure) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten odjazd?')">
                    <i class="fas fa-trash me-2"></i>Usuń odjazd
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informacje o odjeździe #{{ $departure->id }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Trasa</th>
                            <td>{{ $departure->schedule->route->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Linia</th>
                            <td>{{ $departure->schedule->route->line->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Czas odjazdu</th>
                            <td>{{ \Carbon\Carbon::parse($departure->departure_time)->format('H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Dni kursowania</th>
                            <td>
                                @php
                                    $days = [
                                        1 => 'Poniedziałek',
                                        2 => 'Wtorek',
                                        3 => 'Środa',
                                        4 => 'Czwartek',
                                        5 => 'Piątek', 
                                        6 => 'Sobota',
                                        0 => 'Niedziela'
                                    ];
                                    
                                    $activeDays = [];
                                    foreach ($departure->schedule->days_of_week as $day) {
                                        $activeDays[] = $days[$day];
                                    }
                                @endphp
                                {{ implode(', ', $activeDays) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Cena</th>
                            <td>{{ number_format($departure->price, 2, ',', ' ') }} PLN</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Status</th>
                            <td>
                                @if($departure->is_active)
                                    <span class="badge bg-success">Aktywny</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywny</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bus me-2"></i>Informacje o pojeździe</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Numer pojazdu</th>
                            <td>{{ $departure->vehicle->vehicle_number }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Przewoźnik</th>
                            <td>{{ $departure->vehicle->line->carrier->name ?? 'Brak danych' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Typ pojazdu</th>
                            <td>{{ $departure->vehicle->type }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Liczba miejsc</th>
                            <td>{{ $departure->vehicle->capacity }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Status pojazdu</th>
                            <td>
                                @if($departure->vehicle->is_active)
                                    <span class="badge bg-success">Aktywny</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywny</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Przystanki na trasie</h5>
        </div>
        <div class="card-body">
            <ol class="list-group list-group-numbered">
                @foreach($departure->schedule->route->routeStops->sortBy('order') as $routeStop)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">{{ $routeStop->stop->name }}</div>
                            {{ $routeStop->stop->city->name }}
                            
                            @php
                                // Oblicz przybliżony czas przyjazdu na przystanek
                                $departureTime = \Carbon\Carbon::parse($departure->departure_time);
                                $travelMinutes = 0;
                                
                                if ($routeStop->order > 0) {
                                    // Oblicz czas podróży od początku trasy do tego przystanku
                                    $travelMinutes = round($routeStop->travel_time ?? ($routeStop->order * 5));
                                }
                                
                                $arrivalTime = $departureTime->copy()->addMinutes($travelMinutes);
                            @endphp
                            
                            @if($routeStop->order > 0)
                                <div class="text-muted small">
                                    Przybliżony czas przyjazdu: {{ $arrivalTime->format('H:i') }}
                                </div>
                            @else
                                <div class="text-muted small">
                                    Odjazd: {{ $departureTime->format('H:i') }}
                                </div>
                            @endif
                        </div>
                        
                        @if($routeStop->distance > 0 && $routeStop->order > 0)
                            <span class="badge bg-primary rounded-pill">{{ $routeStop->distance/1000 }} km</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    @if($departure->tickets->count() > 0)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Bilety</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pasażer</th>
                            <th>Status</th>
                            <th>Data zakupu</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departure->tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->user->name }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'reserved' => 'warning',
                                            'paid' => 'success',
                                            'used' => 'success',
                                            'cancelled' => 'danger',
                                            'purchased' => 'success',
                                            'canceled' => 'danger'
                                        ][$ticket->status] ?? 'secondary';
                                        
                                        $statusLabel = [
                                            'reserved' => 'Zarezerwowany',
                                            'paid' => 'Opłacony',
                                            'used' => 'Wykorzystany',
                                            'cancelled' => 'Anulowany',
                                            'purchased' => 'Zakupiony',
                                            'canceled' => 'Anulowany'
                                        ][$ticket->status] ?? ucfirst($ticket->status);
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                    @if(!$ticket->is_active)
                                        <span class="badge bg-secondary">Nieaktywny</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-eye me-2"></i>Szczegóły
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
