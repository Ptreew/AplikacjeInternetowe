@extends('layouts.app')

@section('title', 'Szczegóły linii')

@section('header', 'Szczegóły linii')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=lines" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do panelu
            </a>
            <a href="{{ route('admin.lines.edit', $line) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edytuj linię
            </a>
            <form action="{{ route('admin.lines.destroy', $line) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę linię?')">
                    <i class="fas fa-trash"></i> Usuń linię
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informacje o linii</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $line->id }}</td>
                        </tr>
                        <tr>
                            <th>Numer linii:</th>
                            <td>
                            @if($line->number)
                                {{ $line->number }}
                            @else
                                <span class="fst-italic text-muted">Kurs międzymiastowy</span>
                            @endif
                        </td>
                        </tr>
                        <tr>
                            <th>Nazwa:</th>
                            <td>{{ $line->name }}</td>
                        </tr>
                        <tr>
                            <th>Przewoźnik:</th>
                            <td>{{ $line->carrier->name ?? 'Brak przewoźnika' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Kolor:</th>
                            <td>
                                <span class="color-box" style="background-color: {{ $line->color }}; display: inline-block; width: 20px; height: 20px; margin-right: 5px; border: 1px solid #ccc;"></span>
                                {{ $line->color }}
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($line->is_active)
                                    <span class="badge bg-success">Aktywna</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywna</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Data utworzenia:</th>
                            <td>{{ $line->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Ostatnia aktualizacja:</th>
                            <td>{{ $line->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Trasy linii</h5>
        </div>
        <div class="card-body">
            @if($line->routes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nazwa trasy</th>
                                <th>Początek</th>
                                <th>Koniec</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($line->routes as $route)
                                <tr>
                                    <td>{{ $route->id }}</td>
                                    <td>{{ $route->name }}</td>
                                    <td>{{ $route->start_stop->name ?? 'Brak' }}</td>
                                    <td>{{ $route->end_stop->name ?? 'Brak' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Szczegóły</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">Ta linia nie ma jeszcze przypisanych tras.</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pojazdy przypisane do linii</h5>
        </div>
        <div class="card-body">
            @if($line->vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Numer pojazdu</th>
                                <th>Typ</th>
                                <th>Pojemność</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($line->vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->id }}</td>
                                    <td>{{ $vehicle->vehicle_number }}</td>
                                    <td>{{ $vehicle->type }}</td>
                                    <td>{{ $vehicle->capacity }}</td>
                                    <td>
                                        @if($vehicle->is_active)
                                            <span class="badge bg-success">Aktywny</span>
                                        @else
                                            <span class="badge bg-danger">Nieaktywny</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-sm btn-primary">Szczegóły</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">Ta linia nie ma jeszcze przypisanych pojazdów.</div>
            @endif
        </div>
    </div>
</div>
@endsection
