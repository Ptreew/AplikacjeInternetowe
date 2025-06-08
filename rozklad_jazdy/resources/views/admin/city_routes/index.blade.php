@extends('layouts.app')

@section('title', 'Lista Kursów Miejskich')

@section('header', 'Lista Kursów Miejskich')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ url('/admin?tab=miejskie') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu administratora</a>
            </div>
        </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bus me-2"></i>Lista kursów miejskich</h5>
                    <a href="{{ route('admin.city_routes.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-2"></i>Dodaj kurs
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Linia</th>
                                    <th>Nazwa</th>
                                    <th>Przewoźnik</th>
                                    <th>Liczba przystanków</th>
                                    <th>Czas przejazdu</th>
                                    <th>Status</th>
                                    <th class="text-center">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($routes as $route)
                                    <tr>
                                        <td>{{ $route->id }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $route->line->color }}; color: white;">
                                                {{ $route->line->number }}
                                            </span>
                                        </td>
                                        <td>{{ $route->name }}</td>
                                        <td>{{ $route->line->carrier->name }}</td>
                                        <td>{{ $route->routeStops->count() }}</td>
                                        <td>{{ $route->travel_time }} min</td>
                                        <td>
                                            @if($route->is_active)
                                                <span class="badge bg-success">Aktywny</span>
                                            @else
                                                <span class="badge bg-secondary">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.city_routes.edit', $route->id) }}" class="btn btn-sm btn-primary me-1">
                                                    <i class="fas fa-edit me-2"></i>Edytuj
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $route->id }}">
                                                    <i class="fas fa-trash-alt me-2"></i>Usuń
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $route->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $route->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $route->id }}">Potwierdź usunięcie</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Czy na pewno chcesz usunąć kurs linii <strong>{{ $route->line->number }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Anuluj</button>
                                                            <form action="{{ route('admin.city_routes.destroy', $route->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Usuń</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Brak kursów miejskich.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $routes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div> {{-- Close container-fluid --}}
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.9em;
        padding: 0.4em 0.6em;
    }
    
    .table th {
        white-space: nowrap;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush
