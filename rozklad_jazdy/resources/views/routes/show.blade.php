@extends('layouts.app')

@section('title', 'Szczegóły trasy - ' . $route->name)

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Szczegóły trasy</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-light">Powrót</a>
                </div>
                <div class="card-body">
                    <div class="route-details mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4>
                                    <span class="badge bg-success me-2">{{ $route->line->name }}</span>
                                    {{ $route->name }}
                                </h4>
                                <p class="text-muted">
                                    <i class="bi bi-building"></i> 
                                    Przewoźnik: <strong>{{ $route->line->carrier->name }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Route Map/Path Visualization -->
                    <div class="route-visualization mb-4">
                        <h5 class="card-title">Przebieg trasy</h5>
                        <div class="route-path p-3 bg-light rounded">
                            <div class="stops-timeline">
                                @foreach($route->routeStops as $index => $routeStop)
                                    <div class="stop-item d-flex align-items-start">
                                        <div class="stop-marker">
                                            <div class="stop-dot {{ $index == 0 ? 'start' : ($index == count($route->routeStops) - 1 ? 'end' : '') }}"></div>
                                            @if($index < count($route->routeStops) - 1)
                                                <div class="stop-line"></div>
                                            @endif
                                        </div>
                                        <div class="stop-details ms-3 mb-3">
                                            <div class="stop-name fw-bold">{{ $routeStop->stop->name }}</div>
                                            <div class="stop-city text-muted">{{ $routeStop->stop->city->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Schedules and Departures -->
                    <div class="schedules-section">
                        <h5 class="card-title">Rozkłady jazdy</h5>
                        
                        @forelse($route->schedules as $schedule)
                            <div class="schedule-card mb-3 p-3 border rounded">
                                <div class="schedule-header mb-2">
                                    <h6>
                                        <span class="badge bg-secondary">
                                            @php
                                                $dayTypes = [
                                                    'weekday' => 'Dni powszednie (pon-pt)',
                                                    'saturday' => 'Sobota',
                                                    'sunday' => 'Niedziela'
                                                ];
                                            @endphp
                                            {{ $dayTypes[$schedule->day_type] }}
                                        </span>
                                        <small class="text-muted ms-2">
                                            Ważny od {{ date('d.m.Y', strtotime($schedule->valid_from)) }} 
                                            do {{ date('d.m.Y', strtotime($schedule->valid_to)) }}
                                        </small>
                                    </h6>
                                </div>
                                
                                <div class="departures">
                                    @if($schedule->departures && $schedule->departures->count() > 0)
                                        <div class="row">
                                            @foreach($schedule->departures as $departure)
                                                <div class="col-md-3 col-sm-4 mb-3">
                                                    <div class="departure-card p-2 border rounded text-center">
                                                        <div class="departure-time fs-5 fw-bold mb-1">
                                                            {{ date('H:i', strtotime($departure->departure_time)) }}
                                                        </div>
                                                        <div class="vehicle-info small text-muted mb-2">
                                                            {{ $departure->vehicle->type ?? 'Autobus' }} 
                                                            {{ $departure->vehicle->number ?? '' }}
                                                        </div>
                                                        @if(Auth::check())
                                                            <a href="{{ route('tickets.create', ['departure_id' => $departure->id]) }}" class="btn btn-sm btn-primary w-100">
                                                                Zarezerwuj bilet
                                                            </a>
                                                        @else
                                                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary w-100">
                                                                Zaloguj aby kupić bilet
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">Brak zaplanowanych odjazdów dla tego rozkładu.</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">Brak dostępnych rozkładów dla tej trasy.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Route visualization styling */
    .stops-timeline {
        position: relative;
        padding-left: 15px;
    }
    
    .stop-marker {
        position: relative;
        min-height: 50px;
    }
    
    .stop-dot {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background-color: #6c757d;
        position: absolute;
        left: -7.5px;
    }
    
    .stop-dot.start {
        background-color: #198754; /* success color for start */
    }
    
    .stop-dot.end {
        background-color: #dc3545; /* danger color for end */
    }
    
    .stop-line {
        position: absolute;
        left: 0;
        top: 15px;
        bottom: 0;
        width: 2px;
        background-color: #6c757d;
    }
    
    .schedule-card .departure-card {
        transition: all 0.2s;
    }
    
    .schedule-card .departure-card:hover {
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        border-color: #0d6efd;
    }
</style>
@endsection
