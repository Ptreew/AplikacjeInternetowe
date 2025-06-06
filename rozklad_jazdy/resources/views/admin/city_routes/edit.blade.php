@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url('/admin?tab=miejskie') }}" class="btn btn-primary">Powrót do panelu</a>
        </div>
    </div>
    
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
