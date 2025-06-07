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
                               id="departure_time" name="departure_time" value="{{ old('departure_time', now()->format('H:i')) }}" required>
                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">Cena biletu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', 0) }}" 
                                   step="0.01" min="0" required>
                            <span class="input-group-text">PLN</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="stop_id" class="form-label">Przystanek odjazdu <span class="text-danger">*</span></label>
                        <select class="form-select @error('stop_id') is-invalid @enderror" id="stop_id" name="stop_id" required>
                            <option value="">Wybierz przystanek</option>
                            @foreach($stops as $stop)
                                <option value="{{ $stop->id }}" {{ old('stop_id') == $stop->id ? 'selected' : '' }}>
                                    {{ $stop->name }}, {{ $stop->city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('stop_id')
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleSelect = document.getElementById('schedule_id');
        const stopSelect = document.getElementById('stop_id');
        
        // Function to load stops for a selected schedule
        function loadStopsForSchedule(scheduleId) {
            if (!scheduleId) {
                // Clear stops dropdown if no schedule selected
                stopSelect.innerHTML = '<option value="">Wybierz przystanek</option>';
                return;
            }
            
            // Show loading indicator
            stopSelect.innerHTML = '<option value="">Ładowanie przystanków...</option>';
            
            // Fetch stops for the selected schedule
            fetch(`/admin/api/schedules/${scheduleId}/stops`)
                .then(response => response.json())
                .then(data => {
                    // Clear dropdown
                    stopSelect.innerHTML = '<option value="">Wybierz przystanek</option>';
                    
                    // Add stops to dropdown
                    data.stops.forEach((stop, index) => {
                        const option = document.createElement('option');
                        option.value = stop.id;
                        option.textContent = `${stop.name}, ${stop.city.name}`;
                        
                        // Select the first stop by default
                        if (index === 0) {
                            option.selected = true;
                        }
                        
                        stopSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Błąd podczas pobierania przystanków:', error);
                    stopSelect.innerHTML = '<option value="">Błąd pobierania przystanków</option>';
                });
        }
        
        // Initialize stops dropdown based on selected schedule
        if (scheduleSelect.value) {
            loadStopsForSchedule(scheduleSelect.value);
        }
        
        // Update stops when schedule changes
        scheduleSelect.addEventListener('change', function() {
            loadStopsForSchedule(this.value);
        });
    });
</script>
@endsection
