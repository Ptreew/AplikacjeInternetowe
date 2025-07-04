@extends('layouts.app')

@section('title', 'Ulubione linie')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <a href="{{ route('home') }}" class="btn btn-primary mb-3">
                <i class="fas fa-arrow-left"></i> Powrót do strony głównej
            </a>
            <h1><i class="fas fa-star text-warning me-2"></i>Moje ulubione linie</h1>
            
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
                <div class="card-body">
                    <p class="card-text">
                        W tym miejscu zarządzasz swoimi ulubionymi liniami komunikacyjnymi. 
                        Możesz dodawać nowe linie do ulubionych podczas przeglądania 
                        <a href="{{ route('lines.index') }}">listy linii</a>.
                    </p>
                </div>
            </div>

            @if($lines->isEmpty())
                <div class="alert alert-info">
                    <p><i class="fas fa-info-circle me-2"></i>Nie masz jeszcze żadnych ulubionych linii.</p>
                    <a href="{{ route('lines.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-search me-2"></i>Przeglądaj linie
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Numer</th>
                                <th>Nazwa</th>
                                <th>Przewoźnik</th>
                                <th>Typ</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $line)
                                <tr>
                                    <td>
                                        @if($line->number === null)
                                            <span class="badge rounded-pill bg-secondary">
                                                <i class="fas fa-route"></i> IC
                                            </span>
                                        @else
                                            <span class="badge rounded-pill" style="background-color: {{ $line->color ?? '#6c757d' }}">
                                                {{ $line->number }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $line->name }}</td>
                                    <td>{{ $line->carrier->name }}</td>
                                    <td>
                                        @php
                                            $routes = $line->routes;
                                            $routeType = $routes->isNotEmpty() ? $routes->first()->type : 'intercity';
                                        @endphp
                                        @if($routeType == 'city')
                                            <span class="badge bg-success">Miejska</span>
                                        @else
                                            <span class="badge bg-primary">Międzymiastowa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('lines.show', $line) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Szczegóły
                                            </a>
                                            
                                            <form action="{{ route('lines.toggle-favorite', $line) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-link p-0 ms-2" title="Usuń z ulubionych">
                                                    <i class="fas fa-star fa-lg text-warning"></i>
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
                    {{ $lines->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
