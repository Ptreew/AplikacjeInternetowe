@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url('/admin?tab=tickets') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
        </div>
    </div>

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

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Lista biletów</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nr biletu</th>
                            <th>Pasażer</th>
                            <th>Trasa</th>
                            <th>Data odjazdu</th>
                            <th>Status</th>
                            <th>Data zakupu</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_number }}</td>
                                <td>{{ $ticket->passenger_name }}</td>
                                <td>
                                    @if($ticket->departure && $ticket->departure->schedule && $ticket->departure->schedule->route)
                                        @php
                                            $route = $ticket->departure->schedule->route;
                                            $line = $route->line;
                                            $lineName = $line->number ? "Linia {$line->number}" : "<span class='text-muted fst-italic'>{$line->name}</span>";
                                        @endphp
                                        {!! $lineName !!}: {{ $route->name }}
                                    @else
                                        <span class="text-danger">Brak danych</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->departure)
                                        @if($ticket->departure->departure_time instanceof \Carbon\Carbon)
                                            {{ $ticket->departure->departure_time->format('d.m.Y H:i') }}
                                        @else
                                            {{ $ticket->departure->departure_time }}
                                        @endif
                                    @else
                                        <span class="text-danger">Brak danych</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'reserved' => 'warning',
                                            'paid' => 'success',
                                            'used' => 'success',
                                            'cancelled' => 'danger'
                                        ][$ticket->status] ?? 'secondary';
                                        
                                        $statusLabel = [
                                            'reserved' => 'Zarezerwowany',
                                            'paid' => 'Opłacony',
                                            'used' => 'Wykorzystany',
                                            'cancelled' => 'Anulowany'
                                        ][$ticket->status] ?? 'Nieznany';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                    @if(!$ticket->is_active)
                                        <span class="badge bg-secondary">Nieaktywny</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->purchase_date instanceof \Carbon\Carbon)
                                        {{ $ticket->purchase_date->format('d.m.Y') }}
                                    @elseif($ticket->purchase_date)
                                        {{ $ticket->purchase_date }}
                                    @else
                                        <span class="text-muted">Brak danych</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-nowrap justify-content-center">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-success text-white me-1">
                                            <i class="fas fa-eye me-2"></i>Szczegóły
                                        </a>
                                        <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit me-2"></i>Edytuj
                                        </a>
                                        <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten bilet?')">
                                                <i class="fas fa-trash-alt me-2"></i>Usuń
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Brak biletów w systemie.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
