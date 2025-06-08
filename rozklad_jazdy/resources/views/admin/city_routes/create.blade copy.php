@extends('layouts.app')

@section('title', 'Dodaj Kurs Miejski')

@section('header', 'Dodaj Kurs Miejski')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Nowy Kurs Miejski</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.city_routes.store') }}" class="row g-3">
                    @csrf
                    
                    <div class="col-md-4">
                        <label for="carrier_id" class="form-label">Przewoźnik</label>
                        <select class="form-select @error('carrier_id') is-invalid @enderror" id="carrier_id" name="carrier_id" required>
                            <option value="">Wybierz przewoźnika</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                    {{ $carrier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="vehicle_id" class="form-label">Pojazd</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id" required disabled>
                            <option value="">Najpierw wybierz przewoźnika</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-carrier="{{ $vehicle->line->carrier->id ?? '' }}" class="vehicle-option" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }} style="display:none;">
                                    {{ $vehicle->type }} #{{ $vehicle->vehicle_number }} ({{ $vehicle->capacity }} miejsc)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Wybierz pojazd odpowiedniego przewoźnika</div>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-2">
                        <label for="line_number" class="form-label">Numer linii</label>
                        <input type="text" class="form-control @error('line_number') is-invalid @enderror" id="line_number" name="line_number" value="{{ old('line_number') }}" required>
                        <div class="form-text small">Np. 1, 15, 175</div>
                        @error('line_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="line_color" class="form-label">Kolor linii</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="line_color" value="{{ old('line_color', '#3498db') }}">
                            <input type="text" class="form-control @error('line_color') is-invalid @enderror" id="line_color_text" name="line_color" value="{{ old('line_color', '#3498db') }}">
                        </div>
                        @error('line_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="name" class="form-label">Nazwa linii (trasa)</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        <div class="form-text small">Np. "Osiedle Północ - Centrum - Dworzec PKP"</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <h5 class="mt-3 mb-2">Miasto i przystanki</h5>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="city_id" class="form-label">Miasto</label>
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                            <option value="">Wybierz miasto</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}, {{ $city->voivodeship }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Po zapisaniu trasy miejskiej będziesz mógł dodać przystanki i godziny odjazdów.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="travel_time" class="form-label">Czas przejazdu (minuty)</label>
                        <input type="number" min="1" class="form-control @error('travel_time') is-invalid @enderror" id="travel_time" name="travel_time" value="{{ old('travel_time', '30') }}" required>
                        <div class="form-text small">Całkowity czas przejazdu trasy w jedną stronę</div>
                        @error('travel_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">Dni kursowania</label>
                                <div class="row">
                                    @php
                                        $days = [
                                            0 => 'Niedziela',
                                            1 => 'Poniedziałek',
                                            2 => 'Wtorek',
                                            3 => 'Środa',
                                            4 => 'Czwartek',
                                            5 => 'Piątek',
                                            6 => 'Sobota'
                                        ];
                                        $oldDays = old('days_of_week', [1, 2, 3, 4, 5]); // domyślnie Pn-Pt
                                    @endphp
                                    
                                    @foreach($days as $value => $day)
                                        <div class="col-md-4 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $value }}" id="day_{{ $value }}" 
                                                    {{ in_array($value, $oldDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $value }}">{{ $day }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days_of_week')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Kurs aktywny</label>
                        </div>
                        <small class="form-text text-muted">Nieaktywne kursy nie będą widoczne dla użytkowników.</small>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Dodaj kurs</button>
                        <a href="{{ route('admin.city_routes.index') }}" class="btn btn-secondary">Anuluj</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to filter vehicles based on selected carrier
            const carrierSelect = document.getElementById('carrier_id');
            const vehicleSelect = document.getElementById('vehicle_id');
            const vehicleOptions = document.querySelectorAll('.vehicle-option');
            
            function filterVehicles() {
                const selectedCarrierId = carrierSelect.value;
                let hasValidOptions = false;
                
                // Reset select
                vehicleSelect.value = '';
                
                if (!selectedCarrierId) {
                    // If no carrier selected, disable the vehicle dropdown
                    vehicleOptions.forEach(option => {
                        option.style.display = 'none';
                    });
                    vehicleSelect.querySelector('option:first-child').text = 'Najpierw wybierz przewoźnika';
                    vehicleSelect.disabled = true;
                } else {
                    // Enable dropdown and show only vehicles belonging to selected carrier's lines
                    vehicleSelect.disabled = false;
                    vehicleOptions.forEach(option => {
                        const carrierId = option.getAttribute('data-carrier');
                        if (carrierId && carrierId === selectedCarrierId) {
                            option.style.display = '';
                            hasValidOptions = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });
                    vehicleSelect.querySelector('option:first-child').text = hasValidOptions ? 
                        'Wybierz pojazd' : 'Brak dostępnych pojazdów dla tego przewoźnika';
                }
            }
            
            // Initialize vehicles filter
            filterVehicles();
            
            // Listen for carrier changes
            carrierSelect.addEventListener('change', filterVehicles);

            // Handle color synchronization
            const colorPicker = document.getElementById('line_color');
            const colorTextField = document.getElementById('line_color_text');

            colorPicker.addEventListener('input', function() {
                colorTextField.value = this.value;
            });

            colorTextField.addEventListener('input', function() {
                // Check if the value is a valid hex color
                const hexPattern = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
                if (hexPattern.test(this.value)) {
                    colorPicker.value = this.value;
                }
            });

            // Before submitting the form, ensure the correct color format is used
            document.querySelector('form').addEventListener('submit', function(e) {
                const colorValue = colorTextField.value;
                const hexPattern = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
                
                if (!hexPattern.test(colorValue)) {
                    e.preventDefault();
                    alert('Kolor musi być w formacie HEX (#RRGGBB)');
                    colorTextField.focus();
                }
            });
        });
    </script>
@endsection

<!-- Stop Template (Hidden) -->
<template id="stop-template">
    <div class="stop-item card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <label class="form-label">Przystanek <span class="stop-number">1</span> <span class="text-danger">*</span></label>
                    <select class="form-select stop-select" name="stops[0][stop_id]" required>
                        <option value="">Wybierz przystanek</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Czas do następnego przystanku (min) <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control time-to-next" 
                           name="stops[0][time_to_next]" value="5" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-stop" style="margin-bottom: 0.5rem;">
                        <i class="fas fa-trash"></i> Usuń
    const cityIdSelect = document.getElementById('city_id');
    const stopTemplate = document.getElementById('stop-template').content;
    let stopCount = 0;
    
    // Initialize stops if we have validation errors
    const hasOldStops = {!! json_encode(old('stops', false)) !!};
    
    if (hasOldStops) {
        // If we have old input, use that to rebuild the stops
        const oldStops = {!! json_encode(old('stops', [])) !!};
        oldStops.forEach((stop, index) => {
            addStop(stop.stop_id, stop.time_to_next);
        });
    } else {
        // Otherwise add two default stops
        addStop('', 5);
        addStop('', 0);
    }
    
    // Add event listener for city change
    cityIdSelect.addEventListener('change', function() {
        const cityId = this.value;
        if (!cityId) return;
        
        // Update all stop selects with the new city's stops
        updateAllStopSelects(cityId);
    });
    
    // Add stop button click handler
    addStopBtn.addEventListener('click', function() {
        addStop('', 5);
    });
    
    // Delegate event for remove buttons
    stopsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-stop')) {
            const stopItem = e.target.closest('.stop-item');
            if (document.querySelectorAll('.stop-item').length > 2) {
                stopItem.remove();
                updateStopNumbers();
            } else {
                alert('Trasa musi zawierać co najmniej dwa przystanki.');
            }
        }
    });
    
    // Function to add a new stop
    function addStop(selectedStopId = '', timeToNext = 5) {
        const stopItem = stopTemplate.cloneNode(true);
        const stopNumber = stopCount + 1;
        
        // Update the stop number
        stopItem.querySelector('.stop-number').textContent = stopNumber;
        
        // Update the name attributes with the current stop count
        const stopSelect = stopItem.querySelector('.stop-select');
        const timeInput = stopItem.querySelector('.time-to-next');
        
        stopSelect.name = `stops[${stopCount}][stop_id]`;
        timeInput.name = `stops[${stopCount}][time_to_next]`;
        timeInput.value = timeToNext;
        
        // If this is the last stop, disable the time input and set value to 0
        if (stopCount > 0) {
            const lastStop = stopsContainer.lastElementChild;
            if (lastStop) {
                const lastTimeInput = lastStop.querySelector('.time-to-next');
                lastTimeInput.value = '0';
                lastTimeInput.disabled = true;
            }
        }
        
        // Set the selected value if provided
        if (selectedStopId) {
            setTimeout(() => {
                stopSelect.value = selectedStopId;
            }, 0);
        }
        
        // Add the new stop to the container
        stopsContainer.appendChild(document.importNode(stopItem, true));
        
        // If a city is already selected, load its stops
        if (cityIdSelect.value) {
            updateStopSelect(stopCount, cityIdSelect.value, selectedStopId);
        }
        
        stopCount++;
    }
    
    // Function to update all stop selects with stops from the selected city
    function updateAllStopSelects(cityId) {
        const stopItems = document.querySelectorAll('.stop-item');
        stopItems.forEach((item, index) => {
            const stopId = item.querySelector('.stop-select').value;
            updateStopSelect(index, cityId, stopId);
        });
    }
    
    // Function to update a single stop select
    function updateStopSelect(index, cityId, selectedValue = '') {
        fetch(`/admin/city-routes/get-stops?city_id=${cityId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.querySelector(`select[name="stops[${index}][stop_id]"]`);
                    if (!select) return;
                    
                    // Save the current value
                    const currentValue = selectedValue || select.value;
                    
                    // Clear existing options
                    select.innerHTML = '<option value="">Wybierz przystanek</option>';
                    
                    // Add new options
                    data.stops.forEach(stop => {
                        const option = document.createElement('option');
                        option.value = stop.id;
                        option.textContent = `${stop.name} (${stop.address || 'brak adresu'})`;
                        option.selected = (stop.id == currentValue);
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching stops:', error);
            });
    }
    
    // Function to update stop numbers
    function updateStopNumbers() {
        document.querySelectorAll('.stop-item').forEach((item, index) => {
            item.querySelector('.stop-number').textContent = index + 1;
            
            // Update the name attributes
            const stopSelect = item.querySelector('.stop-select');
            const timeInput = item.querySelector('.time-to-next');
            
            if (stopSelect) stopSelect.name = `stops[${index}][stop_id]`;
            if (timeInput) timeInput.name = `stops[${index}][time_to_next]`;
            
            // Enable/disable time inputs
            if (timeInput) {
                timeInput.disabled = (index === document.querySelectorAll('.stop-item').length - 1);
                if (timeInput.disabled) {
                    timeInput.value = '0';
                }
            }
        });
    }
    
    // Update stop numbers on page load
    updateStopNumbers();
});
</script>
@endpush
