@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edytuj kurs miejski: {{ $route->name }}</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.city_routes.update', $route->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="alert alert-info">
                            Edytujesz podstawowe informacje o kursie. Aby zarządzać przystankami, przejdź do sekcji przystanków po zapisaniu zmian.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="carrier_id" class="form-label">Przewoźnik <span class="text-danger">*</span></label>
                                <select class="form-select" id="carrier_id" name="carrier_id" required>
                                    <option value="">Wybierz przewoźnika</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}" {{ old('carrier_id', $route->line->carrier_id) == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                                @error('carrier_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Pojazd <span class="text-danger">*</span></label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Wybierz pojazd</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-carrier="{{ $vehicle->line->carrier_id ?? '' }}" class="vehicle-option" {{ old('vehicle_id', $route->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->type }} {{ $vehicle->vehicle_number }} (Pojemność: {{ $vehicle->capacity ?? 'brak' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="line_number" class="form-label">Numer linii <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="line_number" name="line_number" value="{{ old('line_number', $route->line->number) }}" required>
                                @error('line_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="line_color" class="form-label">Kolor linii</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="line_color" value="{{ old('line_color', $route->line->color) }}" title="Wybierz kolor dla linii">
                                    <input type="text" class="form-control" id="line_color_text" name="line_color" value="{{ old('line_color', $route->line->color) }}" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" required>
                                </div>
                                <small class="form-text text-muted">Format: #RRGGBB (np. #FF5733)</small>
                                @error('line_color')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Nazwa trasy <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $route->name) }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label">Miasto <span class="text-danger">*</span></label>
                                <select class="form-select" id="city_id" name="city_id" required>
                                    <option value="">Wybierz miasto</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $route->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }} ({{ $city->voivodeship }})</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="travel_time" class="form-label">Czas przejazdu (minuty) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="travel_time" name="travel_time" value="{{ old('travel_time', $route->travel_time) }}" min="1" required>
                                @error('travel_time')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Dni kursowania <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($daysOfWeek as $value => $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="day_{{ $value }}" name="days_of_week[]" value="{{ $value }}" 
                                            {{ in_array((int)$value, old('days_of_week', $selectedDaysOfWeek) ?: []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ $value }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days_of_week')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Kurs aktywny</label>
                                </div>
                                <small class="form-text text-muted">Nieaktywne kursy nie będą widoczne dla użytkowników.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                <a href="{{ route('admin.city_routes.index') }}" class="btn btn-secondary">Anuluj</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="carrier_id" class="form-label">Przewoźnik <span class="text-danger">*</span></label>
                                <select class="form-select" id="carrier_id" name="carrier_id" required>
                                    <option value="">Wybierz przewoźnika</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}" {{ old('carrier_id', $route->line->carrier_id) == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                                @error('carrier_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Pojazd <span class="text-danger">*</span></label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Wybierz pojazd</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-carrier="{{ $vehicle->line->carrier_id ?? '' }}" class="vehicle-option" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->type }} {{ $vehicle->vehicle_number }} (Pojemność: {{ $vehicle->capacity ?? 'brak' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="line_number" class="form-label">Numer linii <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="line_number" name="line_number" value="{{ old('line_number', $route->line->number) }}" required>
                                @error('line_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="line_color" class="form-label">Kolor linii</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="line_color" value="{{ old('line_color', $route->line->color) }}" title="Wybierz kolor dla linii">
                                    <input type="text" class="form-control" id="line_color_text" name="line_color" value="{{ old('line_color', $route->line->color) }}" placeholder="#FFFFFF" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" required>
                                </div>
                                <small class="form-text text-muted">Format: #RRGGBB (np. #FF5733)</small>
                                @error('line_color')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Nazwa trasy <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $route->name) }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label">Miasto <span class="text-danger">*</span></label>
                                <select class="form-select" id="city_id" name="city_id" required>
                                    <option value="">Wybierz miasto</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }} ({{ $city->voivodeship }})</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="travel_time" class="form-label">Czas przejazdu (minuty) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="travel_time" name="travel_time" value="{{ old('travel_time', $route->travel_time) }}" min="1" required>
                                @error('travel_time')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Dni kursowania <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($daysOfWeek as $value => $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="day_{{ $value }}" name="days_of_week[]" value="{{ $value }}" 
                                            {{ in_array((int)$value, old('days_of_week', $selectedDaysOfWeek) ?: []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ $value }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days_of_week')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Kurs aktywny</label>
                                </div>
                                <small class="form-text text-muted">Nieaktywne kursy nie będą widoczne dla użytkowników.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                <a href="{{ route('admin.city_routes.index') }}" class="btn btn-secondary">Anuluj</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                                                               {{ $loop->last ? 'disabled' : '' }} required>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        @if(!$loop->first)

                        @endphp
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Rozkład jazdy</h5>
                                <hr>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="departure_time" class="form-label">Godzina odjazdu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                       value="{{ $departure ? date('H:i', strtotime($departure->departure_time)) : '08:00' }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Cena biletu (zł) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" 
                                       value="{{ $departure ? $departure->price : '3.50' }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_id" class="form-label">Pojazd <span class="text-danger">*</span></label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Wybierz pojazd</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $departureVehicleId) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->type }} - {{ $vehicle->vehicle_number ?? 'Brak numeru' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Dni tygodnia <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach($daysOfWeek as $value => $day)
                                        <div class="col-md-3 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="days_of_week[]" value="{{ $value }}" 
                                                       id="day{{ $value }}" {{ in_array($value, $selectedDaysOfWeek) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day{{ $value }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.city_routes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Wróć do listy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Zaktualizuj kurs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stopsContainer = document.getElementById('stops-container');
    const addStopBtn = document.getElementById('addStop');
    const cityIdSelect = document.getElementById('city_id');
    const stopTemplate = document.getElementById('stop-template').content;
    let stopCount = {{ $route->routeStops->count() }};
    
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
