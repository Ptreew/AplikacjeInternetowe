@extends('layouts.app')

@section('title', 'Edytuj odjazd')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.departures.show', $departure) }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do szczegółów odjazdu
            </a>
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
            <h5 class="mb-0">Edytuj odjazd #{{ $departure->id }}</h5>
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

            <form action="{{ route('admin.departures.update', $departure) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="schedule_id" class="form-label">Rozkład jazdy <span class="text-danger">*</span></label>
                        <select class="form-select @error('schedule_id') is-invalid @enderror" id="schedule_id" name="schedule_id" required>
                            <option value="">Wybierz rozkład</option>
                            @foreach($schedules as $schedule)
                                <option value="{{ $schedule->id }}" {{ old('schedule_id', $departure->schedule_id) == $schedule->id ? 'selected' : '' }}>
                                    {{ $schedule->route->name }} ({{ $schedule->route->line->name }})
                                    - {{ isset($schedule->valid_from) ? \Carbon\Carbon::parse($schedule->valid_from)->format('d.m.Y') : 'brak daty' }}
                                    do {{ isset($schedule->valid_to) ? \Carbon\Carbon::parse($schedule->valid_to)->format('d.m.Y') : 'brak daty' }}
                                </option>
                            @endforeach
                        </select>
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
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $departure->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
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
                               id="departure_time" name="departure_time" value="{{ old('departure_time', $departureTime) }}" required>
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
                                   id="price" name="price" value="{{ old('price', $departure->price) }}" 
                                   step="0.01" min="0" required>
                            <span class="input-group-text">PLN</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="stop_id" class="form-label">Przystanek odjazdu <span class="text-danger">*</span></label>
                        <select class="form-select @error('stop_id') is-invalid @enderror" id="stop_id" name="stop_id" required>
                            @if($stops && $stops->count() > 0)
                                @foreach($stops as $stop)
                                    <option value="{{ $stop->id }}" {{ old('stop_id', $departure->stop_id) == $stop->id ? 'selected' : '' }}>
                                        {{ $stop->name }} ({{ $stop->city ? $stop->city->name : 'Nieznane miasto' }})
                                    </option>
                                @endforeach
                            @else
                                <option value="">Najpierw wybierz rozkład</option>
                            @endif
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
                                   {{ old('is_active', $departure->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktywny</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Zapisz zmiany
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleSelect = document.getElementById('schedule_id');
        const stopSelect = document.getElementById('stop_id');
        
        if (scheduleSelect && stopSelect) {
            scheduleSelect.addEventListener('change', function() {
                const scheduleId = this.value;
                if (scheduleId) {
                    // Clear current options
                    stopSelect.innerHTML = '';
                    
                    // Fetch stops for the selected schedule
                    fetch(`/admin/api/schedules/${scheduleId}/stops`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.stops && data.stops.length > 0) {
                                // Add stops to dropdown
                                data.stops.forEach((stop, index) => {
                                    const option = document.createElement('option');
                                    option.value = stop.id;
                                    option.textContent = `${stop.name} (${stop.city ? stop.city.name : 'Nieznane miasto'})`;
                                    
                                    // Select the first stop by default
                                    if (index === 0) {
                                        option.selected = true;
                                    }
                                    
                                    stopSelect.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'Brak przystanków dla tego rozkładu';
                                stopSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching stops:', error);
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Błąd pobierania przystanków';
                            stopSelect.appendChild(option);
                        });
                } else {
                    // Clear stops if no schedule is selected
                    stopSelect.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Najpierw wybierz rozkład';
                    stopSelect.appendChild(option);
                }
            });
        }
    });
</script>
@endpush

@endsection
