@extends('layouts.app')

@section('title', 'Zarządzanie rozkładami jazdy')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between">
            <a href="{{ route('admin') }}" class="btn btn-primary">Powrót do panelu</a>
            <a href="{{ route('admin.schedules.create') }}" class="btn btn-success">Dodaj rozkład</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Lista rozkładów</h5></div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trasa</th>
                        <th>Okres obowiązywania</th>
                        <th>Dni tygodnia</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->route->name }} ({{ $schedule->route->line->name }})</td>
                            <td>{{ $schedule->valid_from }} — {{ $schedule->valid_to }}</td>
                            <td>{{ implode(',', $schedule->days_of_week) }}</td>
                            <td>
                                <a href="{{ route('admin.schedules.show', $schedule) }}" class="btn btn-sm btn-success me-1">Pokaż</a>
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-primary me-1">Edytuj</a>
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno usunąć rozkład?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Brak rozkładów</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center">{{ $schedules->links() }}</div>
        </div>
    </div>
</div>
@endsection
