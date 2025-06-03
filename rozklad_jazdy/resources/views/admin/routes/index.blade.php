@extends('layouts.app')

@section('title', 'Zarządzanie Trasami')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=routes" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do panelu
            </a>
            <a href="{{ route('admin.routes.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Dodaj nową trasę
            </a>
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

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista tras</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Linia</th>
                            <th>Przewoźnik</th>
                            <th>Czas podróży</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routes as $route)
                            <tr>
                                <td>{{ $route->id }}</td>
                                <td>{{ $route->name }}</td>
                                <td>
                                    @if($route->line->number)
                                        {{ $route->line->number }}
                                    @else
                                        <span class="fst-italic text-muted">Kurs międzymiastowy</span>
                                    @endif
                                </td>
                                <td>{{ $route->line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                <td>
                                    @if($route->travel_time)
                                        {{ $route->travel_time }} min
                                    @else
                                        <span class="text-muted">Nie określono</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->is_active)
                                        <span class="badge bg-success">Aktywna</span>
                                    @else
                                        <span class="badge bg-danger">Nieaktywna</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-inline-flex">
                                        <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-primary me-1">
                                            Edytuj
                                        </a>
                                        <a href="{{ route('admin.routes.show', $route) }}" class="btn btn-sm btn-success me-1">
                                            Pokaż
                                        </a>
                                        <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę trasę?')">
                                                Usuń
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Brak tras w bazie danych</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $routes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
