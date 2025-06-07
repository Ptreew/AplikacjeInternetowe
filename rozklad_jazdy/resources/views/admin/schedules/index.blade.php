@extends('layouts.app')

@section('title', 'Zarządzanie rozkładami jazdy')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url('/admin?tab=schedules') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
            <a href="{{ route('admin.schedules.create') }}" class="btn btn-success ms-2"><i class="fas fa-plus me-2"></i>Dodaj rozkład</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lista rozkładów</h5></div>
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
                            <td>
                                {{ $schedule->route->name }}
                                <span class="badge bg-secondary">{{ $schedule->route->line->name }}</span>
                            </td>
                            <td>{{ $schedule->valid_from }} — {{ $schedule->valid_to }}</td>
                            <td>
                                @php
                                    $dayNames = [
                                        0 => 'Nd',
                                        1 => 'Pn',
                                        2 => 'Wt',
                                        3 => 'Śr',
                                        4 => 'Cz',
                                        5 => 'Pt',
                                        6 => 'Sb'
                                    ];
                                    
                                    $daysText = [];
                                    foreach ($schedule->days_of_week as $day) {
                                        $daysText[] = $dayNames[$day];
                                    }
                                    echo implode(', ', $daysText);
                                @endphp
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-nowrap justify-content-center">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}" class="btn btn-sm btn-success me-1">
                                        <i class="fas fa-eye me-2"></i>Szczegóły
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-edit me-2"></i>Edytuj
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno usunąć rozkład?')">
                                            <i class="fas fa-trash-alt me-2"></i>Usuń
                                        </button>
                                    </form>
                                </div>
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
