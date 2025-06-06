@extends('layouts.app')

@section('title', 'Szczegóły rozkładu')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap gap-2">
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do listy rozkładów
            </a>
            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edytuj rozkład
            </a>
            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten rozkład? Usuwa to również wszystkie powiązane odjazdy.')">
                    <i class="fas fa-trash"></i> Usuń rozkład
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

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informacje o rozkładzie #{{ $schedule->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Trasa</th>
                            <td>{{ $schedule->route->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Linia</th>
                            <td>{{ $schedule->route->line->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Okres obowiązywania</th>
                            <td>{{ \Carbon\Carbon::parse($schedule->valid_from)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($schedule->valid_to)->format('d.m.Y') }}</td>
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
                                    foreach ($schedule->days_of_week as $day) {
                                        $activeDays[] = $days[$day];
                                    }
                                @endphp
                                {{ implode(', ', $activeDays) }}
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Przewoźnik</th>
                            <td>{{ $schedule->route->line->carrier->name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Przystanki na trasie</h5>
                    <ol class="list-group list-group-numbered mb-3">
                        @foreach($schedule->route->routeStops->sortBy('order') as $routeStop)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $routeStop->stop->name }}</div>
                                    {{ $routeStop->stop->city->name }}
                                </div>
                                @if($routeStop->distance > 0)
                                    <span class="badge bg-primary rounded-pill">{{ $routeStop->distance/1000 }} km</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Odjazdy</h5>
            <a href="{{ route('admin.departures.create', ['schedule_id' => $schedule->id]) }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Dodaj odjazd
            </a>
        </div>
        <div class="card-body">
            @if($schedule->departures->isEmpty())
                <div class="alert alert-info">
                    Ten rozkład nie ma jeszcze żadnych odjazdów. Dodaj je, aby opublikować kurs.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Godzina odjazdu</th>
                                <th>Pojazd</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedule->departures->sortBy('departure_time') as $departure)
                                <tr>
                                    <td>{{ $departure->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($departure->departure_time)->format('H:i') }}</td>
                                    <td>
                                        {{ $departure->vehicle->carrier->name }} - 
                                        {{ $departure->vehicle->number }}
                                        ({{ $departure->vehicle->type }}, miejsca: {{ $departure->vehicle->seats }})
                                    </td>
                                    <td>
                                        @if($departure->is_active)
                                            <span class="badge bg-success">Aktywny</span>
                                        @else
                                            <span class="badge bg-danger">Nieaktywny</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.departures.edit', $departure) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.departures.destroy', $departure) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno usunąć ten odjazd?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
