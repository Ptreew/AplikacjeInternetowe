@extends('layouts.app')

@section('title', 'Szczegóły trasy')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=routes" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do panelu
            </a>
            <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edytuj trasę
            </a>
            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę trasę?')">
                    <i class="fas fa-trash"></i> Usuń trasę
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informacje o trasie #{{ $route->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nazwa trasy:</strong> {{ $route->name }}</p>
                    <p>
                        <strong>Typ trasy:</strong> 
                        @if($route->type == 'city')
                            <span class="badge bg-info">Miejska</span>
                        @else
                            <span class="badge bg-warning">Międzymiastowa</span>
                        @endif
                    </p>
                    <p><strong>Linia:</strong> 
                        @if($route->line->number)
                            {{ $route->line->number }}
                        @else
                            <span class="fst-italic text-muted">Kurs międzymiastowy</span>
                        @endif
                        - {{ $route->line->name }}
                    </p>
                    <p><strong>Przewoźnik:</strong> {{ $route->line->carrier->name ?? 'Brak przewoźnika' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Czas podróży:</strong> 
                        @if($route->travel_time)
                            {{ $route->travel_time }} min
                        @else
                            <span class="text-muted">Nie określono</span>
                        @endif
                    </p>
                    <p><strong>Status:</strong> 
                        @if($route->is_active)
                            <span class="badge bg-success">Aktywna</span>
                        @else
                            <span class="badge bg-danger">Nieaktywna</span>
                        @endif
                    </p>
                    <p><strong>Data utworzenia:</strong> {{ $route->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Przystanki na trasie</h5>
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addStopModal">
                <i class="fas fa-plus"></i> Dodaj przystanek
            </button>
        </div>
        <div class="card-body">
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
            
            @if($route->routeStops->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nr</th>
                                <th>Przystanek</th>
                                <th>Miasto</th>
                                <th>Odległość od początku</th>
                                <th>Czas do następnego</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->routeStops->sortBy('stop_number') as $routeStop)
                                <tr>
                                    <td>{{ $routeStop->stop_number }}</td>
                                    <td>{{ $routeStop->stop->name }}</td>
                                    <td>{{ $routeStop->stop->city->name ?? 'Brak miasta' }}</td>
                                    <td>{{ number_format(($routeStop->distance_from_start ?? 0) / 1000, 1) }} km</td>
                                    <td>{{ $routeStop->time_to_next ?? '0' }} min</td>
                                    <td>
                                        <div class="d-inline-flex">
                                            <button type="button" class="btn btn-sm btn-primary me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editStopModal{{ $routeStop->id }}">
                                                Edytuj
                                            </button>
                                            <form action="{{ route('admin.route_stops.destroy', $routeStop) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Czy na pewno chcesz usunąć ten przystanek z trasy?')">
                                                    Usuń
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Modal edycji przystanku -->
                                        <div class="modal fade" id="editStopModal{{ $routeStop->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.route_stops.update', $routeStop) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edytuj przystanek {{ $routeStop->stop->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="stop_number" class="form-label">Kolejność przystanku</label>
                                                                <input type="number" class="form-control" id="stop_number" name="stop_number" 
                                                                       value="{{ $routeStop->stop_number }}" min="1" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="distance_from_start" class="form-label">Odległość od początku (km)</label>
                                                                <input type="number" step="0.1" class="form-control" id="distance_from_start" 
                                                                       name="distance_from_start" value="{{ number_format(($routeStop->distance_from_start ?? 0) / 1000, 1, '.', '') }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="time_to_next" class="form-label">Czas do następnego przystanku (min)</label>
                                                                <input type="number" class="form-control" id="time_to_next" name="time_to_next" 
                                                                       value="{{ $routeStop->time_to_next }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Brak przystanków przypisanych do tej trasy.
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal dodawania przystanku -->
    <div class="modal fade" id="addStopModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.route_stops.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route_id" value="{{ $route->id }}">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Dodaj przystanek do trasy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="stop_id" class="form-label">Przystanek <span class="text-danger">*</span></label>
                                <select class="form-select" id="stop_id" name="stop_id" required>
                                    <option value="">Wybierz przystanek</option>
                                    @php
                                        $stops = \App\Models\Stop::with('city')->orderBy('name')->get();
                                    @endphp
                                    @foreach($stops as $stop)
                                        <option value="{{ $stop->id }}">{{ $stop->name }} ({{ $stop->city->name ?? 'Brak miasta' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="stop_number" class="form-label">Kolejność przystanku <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stop_number" name="stop_number" 
                                       value="{{ $route->routeStops->count() + 1 }}" min="1" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="distance_from_start" class="form-label">Odległość od początku (km)</label>
                                <input type="number" step="0.1" class="form-control" id="distance_from_start" name="distance_from_start">
                                <div class="form-text">Odległość od początku trasy w kilometrach. System automatycznie konwertuje na metry przy zapisie.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="time_to_next" class="form-label">Czas do następnego przystanku (min)</label>
                                <input type="number" class="form-control" id="time_to_next" name="time_to_next">
                                <div class="form-text">Szacowany czas przejazdu do następnego przystanku w minutach.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-success">Dodaj przystanek</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Rozkłady jazdy</h5>
        </div>
        <div class="card-body">
            @if($route->schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dni kursowania</th>
                                <th>Ważny od</th>
                                <th>Ważny do</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->schedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->id }}</td>
                                    <td>
                                        @php
                                            $days = [
                                                1 => 'Pn',
                                                2 => 'Wt',
                                                3 => 'Śr',
                                                4 => 'Cz',
                                                5 => 'Pt',
                                                6 => 'So',
                                                7 => 'Nd'
                                            ];
                                            $operatingDays = [];
                                            foreach ($days as $key => $day) {
                                                if ($schedule->{"day_$key"}) {
                                                    $operatingDays[] = $day;
                                                }
                                            }
                                            echo implode(', ', $operatingDays);
                                        @endphp
                                    </td>
                                    <td>{{ $schedule->valid_from ? $schedule->valid_from->format('d.m.Y') : 'Bezterminowo' }}</td>
                                    <td>{{ $schedule->valid_to ? $schedule->valid_to->format('d.m.Y') : 'Bezterminowo' }}</td>
                                    <td>
                                        @if($schedule->is_active)
                                            <span class="badge bg-success">Aktywny</span>
                                        @else
                                            <span class="badge bg-danger">Nieaktywny</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tutaj można dodać przyciski do zarządzania rozkładami jazdy -->
                                        <a href="#" class="btn btn-sm btn-primary">Szczegóły</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Brak rozkładów jazdy przypisanych do tej trasy.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
