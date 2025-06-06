@extends('layouts.app')

@section('title', 'Lista linii')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1>Lista linii komunikacyjnych</h1>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Filtruj wyniki</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('lines.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Szukaj nazwy</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="carrier_id" class="form-label">Przewoźnik</label>
                            <select class="form-select" id="carrier_id" name="carrier_id">
                                <option value="">Wszyscy przewoźnicy</option>
                                @foreach ($carriers as $carrier)
                                    <option value="{{ $carrier->id }}" {{ request('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                        {{ $carrier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filtruj</button>
                        </div>
                    </form>
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

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numer</th>
                            <th>Nazwa</th>
                            <th>Przewoźnik</th>
                            <th class="text-center">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lines as $line)
                            <tr>
                                <td>
                                    @if ($line->number)
                                        <span class="badge rounded-pill" style="background-color: {{ $line->color ?? '#6c757d' }}">
                                            {{ $line->number }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">
                                            <i class="fas fa-route"></i> IC
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $line->name }}</td>
                                <td>{{ $line->carrier->name }}</td>
                                <!-- Usunięto kolumnę typu -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('lines.show', $line) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Szczegóły
                                        </a>
                                        
                                        @auth
                                            @php
                                                $isFavorite = Auth::user()->favouriteLines()
                                                    ->where('line_id', $line->id)
                                                    ->exists();
                                            @endphp
                                            
                                            <form action="{{ route('lines.toggle-favorite', $line) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-link p-0 ms-2" title="{{ $isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}">
                                                    <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-star fa-lg {{ $isFavorite ? 'text-warning' : 'text-dark' }}"></i>
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Brak linii komunikacyjnych spełniających kryteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $lines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
