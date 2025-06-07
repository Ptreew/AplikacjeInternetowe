@extends('layouts.app')

@section('title', 'Edytuj Kurs Międzymiastowy')

@section('header', 'Edytuj Kurs Międzymiastowy')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=miedzymiastowe') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edycja Kursu Międzymiastowego</h5>
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

                <form method="POST" action="{{ route('admin.intercity.update', $route->id) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    
                    <div class="col-md-4">
                        <label for="carrier_id" class="form-label">Przewoźnik</label>
                        <select class="form-select @error('carrier_id') is-invalid @enderror" id="carrier_id" name="carrier_id" required>
                            <option value="">Wybierz przewoźnika</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ (old('carrier_id', $route->line->carrier_id) == $carrier->id) ? 'selected' : '' }}>
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
                                <option value="{{ $vehicle->id }}" data-carrier="{{ $vehicle->line->carrier->id ?? '' }}" class="vehicle-option" {{ old('vehicle_id', $departureVehicleId) == $vehicle->id ? 'selected' : '' }} style="display:none;">
                                    {{ $vehicle->type }} #{{ $vehicle->vehicle_number }} ({{ $vehicle->capacity }} miejsc)
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <input type="hidden" id="line_number" name="line_number" value="INTERCITY">
                    
                    <div class="col-md-3">
                        <label class="form-label">Nazwa linii</label>
                        <input type="text" class="form-control" value="{{ $route->line->name }}" readonly disabled>
                        <div class="form-text small">Nazwa linii zostanie zaktualizowana automatycznie w formacie "Miasto1 - Miasto2"</div>
                        <input type="hidden" id="line_name" name="line_name" value="AUTO_GENERATE">
                        @error('line_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="origin_city_id" class="form-label">Miasto początkowe</label>
                        <select class="form-select @error('origin_city_id') is-invalid @enderror" id="origin_city_id" name="origin_city_id" required>
                            <option value="">Wybierz miasto</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('origin_city_id', $originStop->city->id ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}, {{ $city->voivodeship }}
                                </option>
                            @endforeach
                        </select>
                        @error('origin_city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="origin_stop_id" class="form-label">Przystanek początkowy</label>
                        <select class="form-select @error('origin_stop_id') is-invalid @enderror" id="origin_stop_id" name="origin_stop_id" required>
                            <option value="">Najpierw wybierz miasto</option>
                            <!-- Stops will be loaded by JavaScript -->
                        </select>
                        @error('origin_stop_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="destination_city_id" class="form-label">Miasto docelowe</label>
                        <select class="form-select @error('destination_city_id') is-invalid @enderror" id="destination_city_id" name="destination_city_id" required>
                            <option value="">Wybierz miasto</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('destination_city_id', $destinationStop->city->id ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}, {{ $city->voivodeship }}
                                </option>
                            @endforeach
                        </select>
                        @error('destination_city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="destination_stop_id" class="form-label">Przystanek docelowy</label>
                        <select class="form-select @error('destination_stop_id') is-invalid @enderror" id="destination_stop_id" name="destination_stop_id" required>
                            <option value="">Najpierw wybierz miasto</option>
                            <!-- Stops will be loaded by JavaScript -->
                        </select>
                        @error('destination_stop_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="departure_time" class="form-label">Czas odjazdu</label>
                        <input type="time" class="form-control @error('departure_time') is-invalid @enderror" id="departure_time" name="departure_time" value="{{ old('departure_time', $departureTime) }}" required>
                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="price" class="form-label">Cena biletu (zł)</label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="travel_time" class="form-label">Czas podróży (minuty)</label>
                        <input type="number" min="1" class="form-control @error('travel_time') is-invalid @enderror" id="travel_time" name="travel_time" value="{{ old('travel_time', $route->travel_time ?? 120) }}" required>
                        <div class="form-text small">Całkowity czas przejazdu trasy</div>
                        @error('travel_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
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
                                // Jeśli mamy dane z formularza, użyj ich, w przeciwnym razie pobierz z bazy
                                $scheduleDays = [];
                                if (isset($route->schedules) && $route->schedules->count() > 0) {
                                    $schedule = $route->schedules->first();
                                    if ($schedule) {
                                        $scheduleDays = $schedule->days_of_week;
                                    }
                                }
                                $oldDays = old('days_of_week', $scheduleDays);
                            @endphp
                            
                            @foreach($days as $value => $day)
                                <div class="col-md-auto col-6">
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
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Zapisz zmiany</button>
                        <a href="{{ route('admin.intercity.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Anuluj</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Pass data from the controller to JavaScript
        const cityStopsData = @json($cityStops);
        
        document.addEventListener('DOMContentLoaded', function() {
            // Get form elements
            const originCitySelect = document.getElementById('origin_city_id');
            const destinationCitySelect = document.getElementById('destination_city_id');
            const originStopSelect = document.getElementById('origin_stop_id');
            const destinationStopSelect = document.getElementById('destination_stop_id');
            
            // Get stop IDs from model data
            const originStopId = '{{ $originStop->id ?? '' }}';
            const destinationStopId = '{{ $destinationStop->id ?? '' }}';
            
            // Add event listener for origin city change
            originCitySelect.addEventListener('change', function() {
                loadStopsForCity(this.value, originStopSelect, originStopId);
                updateCitySelections();
            });
            
            // Add event listener for destination city change
            destinationCitySelect.addEventListener('change', function() {
                loadStopsForCity(this.value, destinationStopSelect, destinationStopId);
                updateCitySelections();
            });
            
            // Function to load stops for city from data passed from the controller
            function loadStopsForCity(cityId, targetSelect, selectedStopId) {
                console.log('Załaduj przystanki dla miasta ID:', cityId);
                
                if (!cityId) {
                    targetSelect.innerHTML = '<option value="">Najpierw wybierz miasto</option>';
                    targetSelect.disabled = true;
                    return;
                }
                
                targetSelect.disabled = true;
                
                // Get stops for selected city from data passed from the controller
                const stops = cityStopsData[cityId] || [];
                console.log('Przystanki dla miasta', cityId, ':', stops);
                
                // Clear and update dropdown
                targetSelect.innerHTML = '<option value="">Wybierz przystanek</option>';
                
                if (stops.length > 0) {
                    stops.forEach(stop => {
                        const option = document.createElement('option');
                        option.value = stop.id;
                        option.textContent = stop.name;
                        
                        // Select previously selected stop
                        if (selectedStopId && stop.id == selectedStopId) {
                            option.selected = true;
                        }
                        
                        targetSelect.appendChild(option);
                    });
                    targetSelect.disabled = false;
                } else {
                    targetSelect.innerHTML = '<option value="">Brak przystanków w tym mieście</option>';
                }
            }
            
            // Function to update city selection options - disable already selected option
            function updateCitySelections() {
                // Get selected values
                const originCityId = originCitySelect.value;
                const destinationCityId = destinationCitySelect.value;
                
                // Reset all options
                Array.from(originCitySelect.options).forEach(option => {
                    option.disabled = false;
                });
                Array.from(destinationCitySelect.options).forEach(option => {
                    option.disabled = false;
                });
                
                // Disable the origin city option in the destination city selection
                if (originCityId) {
                    const optionToDisable = destinationCitySelect.querySelector(`option[value="${originCityId}"]`);
                    if (optionToDisable) {
                        optionToDisable.disabled = true;
                        
                        // If the destination city is now disabled, reset the selection
                        if (destinationCityId === originCityId) {
                            destinationCitySelect.value = '';
                            destinationStopSelect.innerHTML = '<option value="">Najpierw wybierz miasto</option>';
                            destinationStopSelect.disabled = true;
                        }
                    }
                }
                
                // Disable the destination city option in the origin city selection
                if (destinationCityId) {
                    const optionToDisable = originCitySelect.querySelector(`option[value="${destinationCityId}"]`);
                    if (optionToDisable) {
                        optionToDisable.disabled = true;
                        
                        // If the origin city is now disabled, reset the selection
                        if (originCityId === destinationCityId) {
                            originCitySelect.value = '';
                            originStopSelect.innerHTML = '<option value="">Najpierw wybierz miasto</option>';
                            originStopSelect.disabled = true;
                        }
                    }
                }
            }
            
            // Initialize form
            updateCitySelections();
            // Automatically load stops if cities are already selected (e.g. on validation errors)
            if (originCitySelect.value) {
                loadStopsForCity(originCitySelect.value, originStopSelect, originStopId);
            }
            
            if (destinationCitySelect.value) {
                loadStopsForCity(destinationCitySelect.value, destinationStopSelect, destinationStopId);
            }
            
            // Function to filter vehicles based on selected carrier
            const carrierSelect = document.getElementById('carrier_id');
            const vehicleSelect = document.getElementById('vehicle_id');
            const vehicleOptions = document.querySelectorAll('.vehicle-option');
            
            function filterVehicles() {
                const selectedCarrierId = carrierSelect.value;
                let hasValidOptions = false;
                
                // Remember currently selected vehicle (if exists)
                const currentlySelectedVehicle = vehicleSelect.value;
                
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
                    // Check if currently selected vehicle belongs to this carrier
                    let currentVehicleValid = false;
                    
                    vehicleOptions.forEach(option => {
                        const carrierId = option.getAttribute('data-carrier');
                        if (carrierId && carrierId === selectedCarrierId) {
                            option.style.display = '';
                            hasValidOptions = true;
                            
                            // Check if this is the currently selected vehicle
                            if (option.value === currentlySelectedVehicle) {
                                currentVehicleValid = true;
                            }
                        } else {
                            option.style.display = 'none';
                        }
                    });
                    vehicleSelect.querySelector('option:first-child').text = hasValidOptions ? 
                        'Wybierz pojazd' : 'Brak dostępnych pojazdów dla tego przewoźnika';
                    
                    // If the currently selected vehicle doesn't belong to this carrier, reset the selection
                    if (!currentVehicleValid) {
                        vehicleSelect.value = '';
                    }
                }
            }
            
            // If carrier is already selected, and vehicle has a value from the controller,
            // unlock the vehicle select and show only vehicles belonging to the selected carrier
            if (carrierSelect.value) {
                const selectedCarrierId = carrierSelect.value;
                
                // Unlock the vehicle select
                vehicleSelect.disabled = false;
                
                // Show only vehicles for the selected carrier
                vehicleOptions.forEach(option => {
                    const carrierId = option.getAttribute('data-carrier');
                    if (carrierId && carrierId === selectedCarrierId) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Change the text of the first option
                vehicleSelect.querySelector('option:first-child').text = 'Wybierz pojazd';
            }
            
            // Initialize vehicles filter
            filterVehicles();
            
            // Listen for carrier changes
            carrierSelect.addEventListener('change', filterVehicles);
        });
        
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
            const rgbPattern = /^rgb\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*\)$/;
            
            if (!hexPattern.test(colorValue) && !rgbPattern.test(colorValue)) {
                e.preventDefault();
                alert('Kolor musi być w formacie HEX (#RRGGBB) lub RGB (rgb(r,g,b))');
                colorTextField.focus();
            }
        });
    </script>
@endsection
