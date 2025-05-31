@extends('layouts.app')

@section('title', 'Wyniki wyszukiwania - ' . $fromStop->name . ' → ' . $toStop->name)

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Wyniki wyszukiwania - kursy miejskie</h3>
                </div>
                <div class="card-body">
                    <div class="search-summary mb-4">
                        <h4>{{ $fromStop->city->name }}</h4>
                        <p><strong>Z:</strong> {{ $fromStop->name }} ({{ $fromStop->code }})</p>
                        <p><strong>Do:</strong> {{ $toStop->name }} ({{ $toStop->code }})</p>
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
                                        <h6>Trasa:</h6>
                                        <div class="stops-timeline">
                                            @php
                                                $fromStopPos = null;
                                                $toStopPos = null;
                                                $routeStops = $route->routeStops->sortBy('stop_number');
                                                
                                                // Find positions
                                                foreach($routeStops as $routeStop) {
                                                    if($routeStop->stop_id == $fromStop->id) {
                                                        $fromStopPos = $routeStop->stop_number;
                                                    }
                                                    if($routeStop->stop_id == $toStop->id) {
                                                        $toStopPos = $routeStop->stop_number;
                                                    }
                                                }
                                            @endphp

                                            <ul class="list-group">
                                                @foreach($routeStops as $routeStop)
                                                    @if($routeStop->stop_number >= $fromStopPos && $routeStop->stop_number <= $toStopPos)
                                                        <li class="list-group-item
                                                            @if($routeStop->stop_id == $fromStop->id) list-group-item-success @endif
                                                            @if($routeStop->stop_id == $toStop->id) list-group-item-danger @endif
                                                        ">
                                                            <strong>{{ $routeStop->stop_number }}.</strong> 
                                                            {{ $routeStop->stop->name }} ({{ $routeStop->stop->code }})
                                                            
                                                            @if($routeStop->stop_id == $fromStop->id)
                                                                <span class="badge bg-success float-end">Początek</span>
                                                            @endif
                                                            
                                                            @if($routeStop->stop_id == $toStop->id)
                                                                <span class="badge bg-danger float-end">Koniec</span>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
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
                                                                    $dayTypes = [
                                                                        'weekday' => 'Dni powszednie (pon-pt)',
                                                                        'saturday' => 'Sobota',
                                                                        'sunday' => 'Niedziela'
                                                                    ];
                                                                @endphp
                                                                {{ $dayTypes[$schedule->day_type] }}
                                                            </strong>
                                                        </p>
                                                        <div class="departures-list">
                                                            <div class="row">
                                                                @foreach($schedule->departures as $departure)
                                                                    <div class="col-md-2 mb-2">
                                                                        <div class="departure-time p-2 border rounded text-center">
                                                                            <strong>{{ date('H:i', strtotime($departure->departure_time)) }}</strong>
                                                                            @if(Auth::check())
                                                                                <a href="{{ route('tickets.create', ['departure_id' => $departure->id]) }}" class="btn btn-sm btn-primary mt-2 d-block">
                                                                                    Bilet
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-2 d-block">
                                                                                    Zaloguj się
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
                            Nie znaleziono tras pomiędzy wskazanymi przystankami.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('routes.search') }}" class="btn btn-secondary">
                            Nowe wyszukiwanie
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-link">
                            Powrót do strony głównej
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
