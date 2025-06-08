@extends('layouts.app')

@section('title', 'Zarządzanie odjazdami')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url('/admin?tab=departures') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
            <a href="{{ route('admin.departures.create') }}" class="btn btn-success ms-2"><i class="fas fa-plus me-2"></i>Dodaj odjazd</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-clock me-2"></i>Lista odjazdów</h5></div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rozkład (Trasa)</th>
                        <th>Czas odjazdu</th>
                        <th>Pojazd</th>
                        <th>Miejsca</th>
                        <th>Cena</th>
                        <th>Aktywny</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departures as $departure)
                        <tr>
                            <td>{{ $departure->id }}</td>
                            <td>
                                {{ $departure->schedule->route->name }}
                                <span class="badge bg-secondary">{{ $departure->schedule->route->line->name }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($departure->departure_time)->format('H:i') }}</td>
                            <td>{{ $departure->vehicle?->vehicle_number }}</td>
                            <td>
                                @php
                                    $capacity = $departure->vehicle->capacity ?? 0;
                                    $availableSeats = $departure->available_seats ?? $capacity;
                                    $percentFull = $capacity > 0 ? 100 - (($availableSeats / $capacity) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users me-1"></i> {{ $availableSeats }}/{{ $capacity }}
                                    @if($percentFull > 80)
                                        <span class="badge bg-danger ms-1">{{ number_format($percentFull, 0) }}%</span>
                                    @elseif($percentFull > 50)
                                        <span class="badge bg-warning ms-1">{{ number_format($percentFull, 0) }}%</span>
                                    @else
                                        <span class="badge bg-success ms-1">{{ number_format($percentFull, 0) }}%</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ number_format($departure->price, 2, ',', ' ') }} PLN</td>
                            <td class="text-center">
                                @if($departure->is_active)
                                    <span class="badge bg-success">Aktywny</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywny</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-nowrap justify-content-center">
                                    <a href="{{ route('admin.departures.show', $departure) }}" class="btn btn-sm btn-success me-1">
                                        <i class="fas fa-eye me-2"></i>Szczegóły
                                    </a>
                                    <a href="{{ route('admin.departures.edit', $departure) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-edit me-2"></i>Edytuj
                                    </a>
                                    <form action="{{ route('admin.departures.destroy', $departure) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno usunąć odjazd?')">
                                            <i class="fas fa-trash-alt me-2"></i>Usuń
                                        </button>
                                    </form>
                                </div>
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
