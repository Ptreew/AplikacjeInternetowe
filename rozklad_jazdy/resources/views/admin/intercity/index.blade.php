@extends('layouts.app')

@section('title', 'Lista Kursów Międzymiastowych')

@section('header', 'Lista Kursów Międzymiastowych')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=miedzymiastowe') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
                <a href="{{ route('admin.intercity.create') }}" class="btn btn-success"><i class="fas fa-plus me-2"></i>Dodaj Nowy Kurs</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-shuttle-van me-2"></i>Kursy Międzymiastowe</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($routes->isEmpty())
                    <div class="alert alert-info">
                        Brak kursów międzymiastowych w bazie danych
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Linia</th>
                                    <th>Przewoźnik</th>
                                    <th>Miasto początkowe</th>
                                    <th>Miasto docelowe</th>
                                    <th>Dni kursowania</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routes as $route)
                                    <tr>
                                        <td>{{ $route->id }}</td>
                                        <td>{{ $route->line->number }} - {{ $route->line->name }}</td>
                                        <td>{{ $route->line->carrier->name }}</td>
                                        <td>
                                            @if($route->routeStops->isNotEmpty() && $route->routeStops->first()->stop && $route->routeStops->first()->stop->city)
                                                {{ $route->routeStops->first()->stop->city->name }}
                                                ({{ $route->routeStops->first()->stop->name }})
                                            @else
                                                Brak danych
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->routeStops->count() > 1 && $route->routeStops->last()->stop && $route->routeStops->last()->stop->city)
                                                {{ $route->routeStops->last()->stop->city->name }}
                                                ({{ $route->routeStops->last()->stop->name }})
                                            @else
                                                Brak danych
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->schedules->isNotEmpty())
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
                                                    
                                                    $allDays = [];
                                                    foreach($route->schedules as $schedule) {
                                                        if (!is_null($schedule->days_of_week) && is_array($schedule->days_of_week)) {
                                                            foreach($schedule->days_of_week as $day) {
                                                                if (!in_array($day, array_keys($allDays))) {
                                                                    $allDays[$day] = $dayNames[$day];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ksort($allDays);
                                                @endphp
                                                {{ implode(', ', $allDays) }}
                                            @else
                                                <span class="text-muted">Brak danych</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->is_active)
                                                <span class="badge bg-success">Aktywny</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.intercity.edit', $route->id) }}" class="btn btn-sm btn-primary me-1">
                                                    <i class="fas fa-edit me-2"></i>Edytuj
                                                </a>
                                                <form action="{{ route('admin.intercity.destroy', $route->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten kurs?')">
                                                        <i class="fas fa-trash-alt me-2"></i>Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $routes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
