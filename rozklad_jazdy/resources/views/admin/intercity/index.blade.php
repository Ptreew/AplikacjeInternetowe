@extends('layouts.app')

@section('title', 'Lista Kursów Międzymiastowych')

@section('header', 'Lista Kursów Międzymiastowych')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=miedzymiastowe') }}" class="btn btn-primary">Powrót do panelu</a>
                <a href="{{ route('admin.intercity.create') }}" class="btn btn-success">Dodaj Nowy Kurs</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Kursy Międzymiastowe</h5>
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
                                            @if($route->is_active)
                                                <span class="badge bg-success">Aktywny</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.intercity.edit', $route->id) }}" class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <form action="{{ route('admin.intercity.destroy', $route->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten kurs?')">
                                                        Usuń
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
