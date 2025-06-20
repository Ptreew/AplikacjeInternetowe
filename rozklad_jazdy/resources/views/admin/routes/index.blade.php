@extends('layouts.app')

@section('title', 'Zarządzanie Trasami')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=routes" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Powrót do panelu
            </a>
            <a href="{{ route('admin.routes.builder.step1') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Dodaj nową trasę (Builder)
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
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('admin.routes.index') }}" method="GET" class="d-flex">
                        <select name="type" class="form-select me-2" onchange="this.form.submit()">
                            <option value="">Wszystkie typy tras</option>
                            @foreach($routeTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type == 'city' ? 'Miejskie' : 'Międzymiastowe' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filtruj</button>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Typ</th>
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
                                    @if($route->type == 'city')
                                        <span class="badge bg-success">Miejska</span>
                                    @else
                                        <span class="badge bg-primary">Międzymiastowa</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->line->number)
                                        <span class="badge" style="background-color: {{ $route->line->color ?? '#6c757d' }}; color: #fff;">{{ $route->line->number }}</span>
                                    @else
                                        <span class="badge bg-secondary text-white"><i class="fas fa-route"></i> <span style="font-weight: bold;">IC</span></span>
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
                                <td class="text-center">
                                    <div class="d-flex flex-nowrap justify-content-center">
                                        <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit me-1"></i>Edytuj
                                        </a>
                                        <a href="{{ route('admin.routes.show', $route) }}" class="btn btn-sm btn-success me-1">
                                            <i class="fas fa-eye me-1"></i>Pokaż
                                        </a>
                                        <form action="{{ route('admin.routes.destroy', $route) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę trasę?')">
                                                <i class="fas fa-trash-alt me-1"></i>Usuń
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Brak tras w bazie danych</td>
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
