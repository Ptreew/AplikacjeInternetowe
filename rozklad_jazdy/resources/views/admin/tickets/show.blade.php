@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary me-2"><i class="fas fa-arrow-left me-2"></i>Powrót do listy biletów</a>
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-primary me-2"><i class="fas fa-edit me-2"></i>Edytuj</a>
            <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten bilet?')">
                    <i class="fas fa-trash-alt me-2"></i>Usuń
                </button>
            </form>
        </div>
    </div>
    
    <h2 class="mb-3"><i class="fas fa-ticket-alt me-2"></i>Szczegóły biletu</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informacje o bilecie</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Numer biletu:</div>
                        <div class="col-md-8">{{ $ticket->ticket_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @php
                                $statusClass = [
                                    'reserved' => 'warning',
                                    'paid' => 'success',
                                    'used' => 'success',
                                    'cancelled' => 'danger'
                                ][$ticket->status] ?? 'secondary';
                                
                                $statusLabel = [
                                    'reserved' => 'Zarezerwowany',
                                    'paid' => 'Opłacony',
                                    'used' => 'Wykorzystany',
                                    'cancelled' => 'Anulowany'
                                ][$ticket->status] ?? 'Nieznany';
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                            @if(!$ticket->is_active)
                                <span class="badge bg-secondary ms-1">Nieaktywny</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Data zakupu:</div>
                        <div class="col-md-8">
                            @if($ticket->purchase_date instanceof \Carbon\Carbon)
                                {{ $ticket->purchase_date->format('d.m.Y H:i') }}
                            @elseif($ticket->purchase_date)
                                {{ $ticket->purchase_date }}
                            @else
                                <span class="text-muted">Brak danych</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Cena:</div>
                        <div class="col-md-8">
                            @if($ticket->departure)
                                {{ number_format($ticket->getPrice(), 2, ',', ' ') }} zł
                            @else
                                <span class="text-danger">Brak danych</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Uwagi:</div>
                        <div class="col-md-8">{{ $ticket->notes ?: 'Brak' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Dane pasażera</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Imię i nazwisko:</div>
                        <div class="col-md-8">{{ $ticket->passenger_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">{{ $ticket->passenger_email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Telefon:</div>
                        <div class="col-md-8">{{ $ticket->passenger_phone ?: 'Brak' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Konto użytkownika:</div>
                        <div class="col-md-8">
                            @if($ticket->user)
                                {{ $ticket->user->name }} ({{ $ticket->user->email }})
                            @else
                                <span class="text-danger">Brak powiązanego użytkownika</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-route me-2"></i>Informacje o przejeździe</h5>
                </div>
                <div class="card-body">
                    @if($ticket->departure && $ticket->departure->schedule && $ticket->departure->schedule->route)
                        @php
                            $departure = $ticket->departure;
                            $schedule = $departure->schedule;
                            $route = $schedule->route;
                            $line = $route->line;
                        @endphp
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Linia:</div>
                            <div class="col-md-8">
                                @if($line->number)
                                    Linia {{ $line->number }}
                                @else
                                    <span class="text-muted fst-italic">{{ $line->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Trasa:</div>
                            <div class="col-md-8">{{ $route->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Przewoźnik:</div>
                            <div class="col-md-8">{{ $line->carrier->name ?? 'Brak danych' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Pojazd:</div>
                            <div class="col-md-8">
                                @if(isset($departure->vehicle) && $departure->vehicle)
                                    {{ $departure->vehicle->type }} ({{ $departure->vehicle->vehicle_number }})
                                @else
                                    @if(isset($departure->vehicle_id) && $departure->vehicle_id)
                                        <span class="text-muted">ID pojazdu: {{ $departure->vehicle_id }} (brak danych)</span>
                                    @else
                                        <span class="text-muted">Brak przypisanego pojazdu</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Data odjazdu:</div>
                            <div class="col-md-8">
                                @if($departure->departure_time instanceof \Carbon\Carbon)
                                    {{ $departure->departure_time->format('d.m.Y H:i') }}
                                @else
                                    {{ $departure->departure_time }}
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Czas podróży:</div>
                            <div class="col-md-8">{{ $route->travel_time ?? 'Brak danych' }} minut</div>
                        </div>

                    @else
                        <div class="alert alert-danger">
                            Brak danych o przejeździe. Możliwe, że odjazd został usunięty lub zmieniony.
                        </div>
                    @endif
                </div>
            </div>

            @if($ticket->departure && $ticket->departure->schedule && $ticket->departure->schedule->route && $ticket->departure->schedule->route->routeStops->count() > 0)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Przystanki na trasie</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Przystanek</th>
                                        <th>Miasto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticket->departure->schedule->route->routeStops->sortBy('stop_number') as $routeStop)
                                        <tr>
                                            <td>{{ $routeStop->stop_number }}</td>
                                            <td>{{ $routeStop->stop->name }}</td>
                                            <td>{{ $routeStop->stop->city->name ?? 'Brak miasta' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
