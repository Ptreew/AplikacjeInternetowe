@extends('layouts.app')

@section('title', 'Moje ulubione linie')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Moje ulubione linie</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista ulubionych linii</h5>
            <a href="{{ route('lines.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Dodaj nową linię do ulubionych
            </a>
        </div>
        <div class="card-body">
            @if($favouriteLines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Numer linii</th>
                                <th>Nazwa</th>
                                <th>Przewoźnik</th>
                                <th>Typ</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($favouriteLines as $favourite)
                                <tr>
                                    <td>
                                        <span class="badge rounded-pill" style="background-color: {{ $favourite->line->color ?? '#6c757d' }}">
                                            {{ $favourite->line->number ?? 'Brak numeru' }}
                                        </span>
                                    </td>
                                    <td>{{ $favourite->line->name }}</td>
                                    <td>{{ $favourite->line->carrier->name }}</td>
                                    <td>
                                        @php
                                            $routeType = $favourite->line->routes->isNotEmpty() 
                                                ? $favourite->line->routes->first()->type 
                                                : 'nieznany';
                                            $typeLabel = $routeType == 'city' ? 'Miejski' : 'Międzymiastowy';
                                            $typeClass = $routeType == 'city' ? 'success' : 'info';
                                        @endphp
                                        <span class="badge bg-{{ $typeClass }}">{{ $typeLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex">
                                            <a href="{{ route('lines.show', $favourite->line) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye"></i> Pokaż
                                            </a>
                                            <form action="{{ route('favourite-lines.destroy', $favourite) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę linię z ulubionych?')">
                                                    <i class="fas fa-trash"></i> Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $favouriteLines->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    Nie masz jeszcze żadnych ulubionych linii. Możesz dodać linie do ulubionych przeglądając dostępne linie.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
