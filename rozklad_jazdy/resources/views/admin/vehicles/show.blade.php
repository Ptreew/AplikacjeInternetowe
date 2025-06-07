@extends('layouts.app')

@section('title', 'Szczegóły pojazdu')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do listy pojazdów</a>
                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edytuj</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Szczegóły pojazdu: {{ $vehicle->vehicle_number }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4"><i class="fas fa-hashtag me-2"></i>ID:</dt>
                            <dd class="col-sm-8">{{ $vehicle->id }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-id-card me-2"></i>Numer pojazdu:</dt>
                            <dd class="col-sm-8">{{ $vehicle->vehicle_number }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-bus me-2"></i>Typ pojazdu:</dt>
                            <dd class="col-sm-8">{{ $vehicle->type }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-route me-2"></i>Linia:</dt>
                            <dd class="col-sm-8">{{ $vehicle->line->name ?? 'Brak przypisanej linii' }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-building me-2"></i>Przewoźnik:</dt>
                            <dd class="col-sm-8">{{ $vehicle->line->carrier->name ?? 'Brak przewoźnika' }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-users me-2"></i>Pojemność:</dt>
                            <dd class="col-sm-8">{{ $vehicle->capacity }} miejsc</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-toggle-on me-2"></i>Status:</dt>
                            <dd class="col-sm-8">
                                @if($vehicle->is_active)
                                    <span class="badge bg-success">Aktywny</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywny</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-calendar-plus me-2"></i>Data utworzenia:</dt>
                            <dd class="col-sm-8">{{ $vehicle->created_at->format('d.m.Y H:i') }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-calendar-check me-2"></i>Data aktualizacji:</dt>
                            <dd class="col-sm-8">{{ $vehicle->updated_at->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-image me-2"></i>Zdjęcie pojazdu</h5>
                        @if($vehicle->image_path)
                            <img src="{{ asset('storage/' . $vehicle->image_path) }}" alt="{{ $vehicle->vehicle_number }}" class="img-fluid img-thumbnail" style="max-height: 300px">
                        @else
                            <div class="alert alert-light border">
                                <i class="fas fa-camera-retro me-2"></i>Brak zdjęcia dla tego pojazdu
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Zaplanowane odjazdy</h5>
            </div>
            <div class="card-body">
                @if($vehicle->departures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Linia</th>
                                    <th>Data odjazdu</th>
                                    <th>Przystanek początkowy</th>
                                    <th>Przystanek końcowy</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->departures as $departure)
                                    <tr>
                                        <td>{{ $departure->id }}</td>
                                        <td>
                                            @if($departure->schedule && $departure->schedule->route && $departure->schedule->route->line)
                                                {{ $departure->schedule->route->line->name }}
                                            @else
                                                Brak
                                            @endif
                                        </td>
                                        <td>{{ $departure->departure_time }}</td>
                                        <td>
                                            @php
                                                $routeStop = $departure->schedule && $departure->schedule->route ? 
                                                    $departure->schedule->route->routeStops()->orderBy('stop_number', 'asc')->first() : null;
                                                echo $routeStop && $routeStop->stop ? $routeStop->stop->name : 'Brak';
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                $routeStop = $departure->schedule && $departure->schedule->route ? 
                                                    $departure->schedule->route->routeStops()->orderBy('stop_number', 'desc')->first() : null;
                                                echo $routeStop && $routeStop->stop ? $routeStop->stop->name : 'Brak';
                                            @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.departures.show', $departure->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-info-circle me-2"></i>Szczegóły
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">Ten pojazd nie ma zaplanowanych odjazdów.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
