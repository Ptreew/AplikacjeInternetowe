@extends('layouts.app')

@section('title', 'Zarządzanie odjazdami')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between">
            <a href="{{ route('admin') }}" class="btn btn-primary">Powrót do panelu</a>
            <a href="{{ route('admin.departures.create') }}" class="btn btn-success">Dodaj odjazd</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Lista odjazdów</h5></div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rozkład (Trasa)</th>
                        <th>Czas odjazdu</th>
                        <th>Pojazd</th>
                        <th>Aktywny</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departures as $departure)
                        <tr>
                            <td>{{ $departure->id }}</td>
                            <td>{{ $departure->schedule->route->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($departure->departure_time)->format('H:i') }}</td>
                            <td>{{ $departure->vehicle?->vehicle_number }}</td>
                            <td>{{ $departure->is_active ? 'Tak' : 'Nie' }}</td>
                            <td>
                                <a href="{{ route('admin.departures.show', $departure) }}" class="btn btn-sm btn-success me-1">Pokaż</a>
                                <a href="{{ route('admin.departures.edit', $departure) }}" class="btn btn-sm btn-primary me-1">Edytuj</a>
                                <form action="{{ route('admin.departures.destroy', $departure) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno usunąć odjazd?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Brak odjazdów</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center">{{ $departures->links() }}</div>
        </div>
    </div>
</div>
@endsection
