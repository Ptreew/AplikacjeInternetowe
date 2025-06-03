@extends('layouts.app')

@section('title', 'Wyniki wyszukiwania - ' . $fromCity->name . ' → ' . $toCity->name)

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Wyniki wyszukiwania</h3>
                </div>
                <div class="card-body">
                    <div class="search-summary mb-4">
                        <h4>{{ $fromCity->name }} → {{ $toCity->name }}</h4>
                        @if($request->filled('date'))
                            <p><strong>Data:</strong> {{ date('d.m.Y', strtotime($request->date)) }}</p>
                        @endif
                        @if($request->filled('time_from') || $request->filled('time_to'))
                            <p>
                                <strong>Godzina:</strong>
                                @if($request->filled('time_from'))
                                    od {{ $request->time_from }}
                                @endif
                                @if($request->filled('time_to'))
                                    do {{ $request->time_to }}
                                @endif
                            </p>
                        @endif
                    </div>

                    @if($routes->count() > 0)
                        <div class="routes-list">
                            @foreach($routes as $route)
                                <div class="route-card mb-4 p-3 border rounded">
                                    <div class="route-header mb-3">
                                        <h5>
                                            <span class="badge bg-success">{{ $route->line->name }}</span>
                                            {{ $route->name }}
                                        </h5>
                                        <p class="text-muted mb-1">
                                            <i class="bi bi-building"></i> 
                                            Przewoźnik: {{ $route->line->carrier->name }}
                                        </p>
                                    </div>

                                    <div class="route-stops mb-3">
                                        <h6>Przystanki:</h6>
                                        <div class="stops-list">
                                            @php
                                                $fromStopFound = false;
                                                $toStopFound = false;
                                                $fromCityStops = collect();
                                                $toCityStops = collect();
                                                
                                                // Group stops by city
                                                foreach($route->routeStops as $routeStop) {
                                                    if($routeStop->stop->city_id == $fromCity->id) {
                                                        $fromCityStops->push($routeStop);
                                                        $fromStopFound = true;
                                                    }
                                                    if($routeStop->stop->city_id == $toCity->id) {
                                                        $toCityStops->push($routeStop);
                                                        $toStopFound = true;
                                                    }
                                                }
                                            @endphp

                                            @if($fromStopFound && $toStopFound)
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="from-stops">
                                                            <p class="text-primary mb-1">Przystanki początkowe ({{ $fromCity->name }}):</p>
                                                            <ul class="list-group">
                                                                @foreach($fromCityStops as $routeStop)
                                                                    <li class="list-group-item">
                                                                        {{ $routeStop->stop->name }} ({{ $routeStop->stop->code }})
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <i class="bi bi-arrow-right fs-3"></i>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="to-stops">
                                                            <p class="text-primary mb-1">Przystanki docelowe ({{ $toCity->name }}):</p>
                                                            <ul class="list-group">
                                                                @foreach($toCityStops as $routeStop)
                                                                    <li class="list-group-item">
                                                                        {{ $routeStop->stop->name }} ({{ $routeStop->stop->code }})
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="alert alert-warning">Brak pełnych informacji o przystankach.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="route-departures mt-4">
                                        <h6>Odjazdy:</h6>
                                        @if($route->schedules->isNotEmpty())
                                            @foreach($route->schedules as $schedule)
                                                @if($schedule->departures->isNotEmpty())
                                                    <div class="schedule-card mb-3 p-2 bg-light rounded">
                                                        <p class="mb-2">
                                                            <strong>
                                                                @php
                                                                    $dayNames = [
                                                                        0 => 'Nd',
                                                                        1 => 'Pn',
                                                                        2 => 'Wt',
                                                                        3 => 'Śr',
                                                                        4 => 'Cz',
                                                                        5 => 'Pt',
                                                                        6 => 'Sb'
                                                                    ];
                                                                    
                                                                    $daysText = [];
                                                                    foreach ($schedule->days_of_week as $day) {
                                                                        $daysText[] = $dayNames[$day];
                                                                    }
                                                                @endphp
                                                                {{ implode(', ', $daysText) }}
                                                            </strong>
                                                        </p>
                                                        <div class="departures-list">
                                                            <div class="row">
                                                                @foreach($schedule->departures as $departure)
                                                                    <div class="col-md-3 mb-2">
                                                                        <div class="departure-time p-2 border rounded text-center">
                                                                            <strong>{{ date('H:i', strtotime($departure->departure_time)) }}</strong>
                                                                            <div class="small text-muted">
                                                                                {{ $departure->vehicle->type }} {{ $departure->vehicle->number }}
                                                                            </div>
                                                                            <div class="text-success fw-bold">
                                                                                {{ number_format($departure->price, 2) }} zł
                                                                            </div>
                                                                            @if(Auth::check())
                                                                                <a href="{{ route('tickets.create', ['departure_id' => $departure->id]) }}" class="btn btn-sm btn-primary mt-2">
                                                                                    Zarezerwuj bilet
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                                                    Zaloguj się, aby kupić bilet
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="alert alert-info">Brak zaplanowanych odjazdów.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            Nie znaleziono tras pomiędzy wskazanymi miastami.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            Nowe wyszukiwanie
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
