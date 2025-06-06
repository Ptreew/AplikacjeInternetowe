@extends('layouts.app')

@section('title', 'Dodaj nowy odjazd')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            @if(isset($selectedScheduleId))
                <a href="{{ route('admin.schedules.show', $selectedScheduleId) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Powrót do rozkładu
                </a>
            @else
                <a href="{{ route('admin.departures.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Powrót do listy odjazdów
                </a>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Dodaj nowy odjazd</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.departures.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="schedule_id" class="form-label">Rozkład jazdy <span class="text-danger">*</span></label>
                        <select class="form-select @error('schedule_id') is-invalid @enderror" id="schedule_id" name="schedule_id" required {{ isset($selectedScheduleId) ? 'disabled' : '' }}>
                            <option value="">Wybierz rozkład</option>
                            @foreach($schedules as $scheduleItem)
                                <option value="{{ $scheduleItem->id }}" 
                                       {{ old('schedule_id', $selectedScheduleId ?? null) == $scheduleItem->id ? 'selected' : '' }}
                                       data-route-id="{{ $scheduleItem->route_id }}">
                                    {{ $scheduleItem->route->name }} ({{ $scheduleItem->route->line->name }})
                                    - {{ isset($scheduleItem->valid_from) ? \Carbon\Carbon::parse($scheduleItem->valid_from)->format('d.m.Y') : 'brak daty' }}
                                    do {{ isset($scheduleItem->valid_to) ? \Carbon\Carbon::parse($scheduleItem->valid_to)->format('d.m.Y') : 'brak daty' }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($selectedScheduleId))
                            <input type="hidden" name="schedule_id" value="{{ $selectedScheduleId }}">
                        @endif
                        @error('schedule_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Pojazd <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id" required>
                            <option value="">Wybierz pojazd</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_number }} - {{ $vehicle->line && $vehicle->line->carrier ? $vehicle->line->carrier->name : 'Brak przewoźnika' }}
                                    ({{ $vehicle->type }}, miejsca: {{ $vehicle->capacity }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="departure_time" class="form-label">Godzina odjazdu <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('departure_time') is-invalid @enderror" 
                               id="departure_time" name="departure_time" value="{{ old('departure_time') }}" required>
                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktywny</label>
                        </div>
                    </div>
                </div>
                
                @if(isset($schedule))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Przystanki na trasie</h6>
                            </div>
                            <div class="card-body p-0">
                                <ol class="list-group list-group-numbered">
                                    @foreach($schedule->route->routeStops->sortBy('order') as $routeStop)
                                        <li class="list-group-item">
                                            {{ $routeStop->stop->name }}, {{ $routeStop->stop->city->name }}
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Zapisz odjazd
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
