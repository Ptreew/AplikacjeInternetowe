@extends('layouts.app')

@section('title', 'Szczegóły pojazdu')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-primary">Powrót do listy pojazdów</a>
                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-warning">Edytuj</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Szczegóły pojazdu: {{ $vehicle->vehicle_number }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8">{{ $vehicle->id }}</dd>
                            
                            <dt class="col-sm-4">Numer pojazdu:</dt>
                            <dd class="col-sm-8">{{ $vehicle->vehicle_number }}</dd>
                            
                            <dt class="col-sm-4">Typ pojazdu:</dt>
                            <dd class="col-sm-8">{{ $vehicle->type }}</dd>
                            
                            <dt class="col-sm-4">Linia:</dt>
                            <dd class="col-sm-8">{{ $vehicle->line->name ?? 'Brak przypisanej linii' }}</dd>
                            
                            <dt class="col-sm-4">Przewoźnik:</dt>
                            <dd class="col-sm-8">{{ $vehicle->line->carrier->name ?? 'Brak przewoźnika' }}</dd>
                            
                            <dt class="col-sm-4">Pojemność:</dt>
                            <dd class="col-sm-8">{{ $vehicle->capacity }} miejsc</dd>
                            
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                @if($vehicle->is_active)
                                    <span class="badge bg-success">Aktywny</span>
                                @else
                                    <span class="badge bg-danger">Nieaktywny</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Data utworzenia:</dt>
                            <dd class="col-sm-8">{{ $vehicle->created_at->format('d.m.Y H:i') }}</dd>
                            
                            <dt class="col-sm-4">Data aktualizacji:</dt>
                            <dd class="col-sm-8">{{ $vehicle->updated_at->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zaplanowane odjazdy</h5>
            </div>
            <div class="card-body">
                @if($vehicle->departures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Linia</th>
                                    <th>Data odjazdu</th>
                                    <th>Przystanek początkowy</th>
                                    <th>Przystanek końcowy</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->departures as $departure)
                                    <tr>
                                        <td>{{ $departure->id }}</td>
                                        <td>{{ $departure->line->name ?? 'Brak' }}</td>
                                        <td>{{ $departure->departure_time }}</td>
                                        <td>{{ $departure->stop_from->name ?? 'Brak' }}</td>
                                        <td>{{ $departure->stop_to->name ?? 'Brak' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                Szczegóły
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">Ten pojazd nie ma zaplanowanych odjazdów.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
