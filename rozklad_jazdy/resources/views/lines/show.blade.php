@extends('layouts.app')

@section('title', 'Szczegóły linii: ' . $line->name)

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Linia: <span class="badge rounded-pill" style="background-color: {{ $line->color ?? '#6c757d' }}">
                    {{ $line->number }}
                </span> {{ $line->name }}</h1>
                <div>
                    <a href="{{ route('lines.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Powrót do listy
                    </a>
                    @auth
                        <form action="{{ route('lines.toggle-favorite', $line) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 ms-2" title="{{ $isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}" style="font-size: 1.5rem;">
                                <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-star fa-lg {{ $isFavorite ? 'text-warning' : 'text-dark' }}"></i>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informacje o linii</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Numer linii:</strong> {{ $line->number }}</p>
                            <p><strong>Nazwa:</strong> {{ $line->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Przewoźnik:</strong> {{ $line->carrier->name }}</p>
                            <p><strong>Typ transportu:</strong> 
                                @php
                                    $typeLabels = [
                                        'train' => ['Pociąg', 'info'],
                                        'bus' => ['Autobus', 'success'],
                                        'tram' => ['Tramwaj', 'primary'],
                                        'metro' => ['Metro', 'dark'],
                                        'ferry' => ['Prom', 'warning']
                                    ];
                                    
                                    $typeInfo = $typeLabels[$line->type] ?? ['Inny', 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Trasy obsługiwane przez linię</h5>
                </div>
                <div class="card-body">
                    @if($line->routes->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nazwa trasy</th>
                                        <th>Typ</th>
                                        <th>Pierwszy przystanek</th>
                                        <th>Ostatni przystanek</th>
                                        <th>Liczba przystanków</th>
                                        <th>Akcje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($line->routes as $route)
                                        <tr>
                                            <td>{{ $route->name }}</td>
                                            <td>
                                                @if($route->type == 'city')
                                                    <span class="badge bg-success">Miejska</span>
                                                @else
                                                    <span class="badge bg-info">Międzymiastowa</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($route->routeStops->isNotEmpty())
                                                    @php
                                                        $firstStop = $route->routeStops->sortBy('stop_number')->first()->stop;
                                                    @endphp
                                                    {{ $firstStop->name }} ({{ $firstStop->city->name }})
                                                @else
                                                    <span class="text-muted">Brak danych</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($route->routeStops->isNotEmpty())
                                                    @php
                                                        $lastStop = $route->routeStops->sortBy('stop_number')->last()->stop;
                                                    @endphp
                                                    {{ $lastStop->name }} ({{ $lastStop->city->name }})
                                                @else
                                                    <span class="text-muted">Brak danych</span>
                                                @endif
                                            </td>
                                            <td>{{ $route->routeStops->count() }}</td>
                                            <td>
                                                <a href="{{ route('routes.show', $route) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Szczegóły
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Ta linia nie ma jeszcze przypisanych tras.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
