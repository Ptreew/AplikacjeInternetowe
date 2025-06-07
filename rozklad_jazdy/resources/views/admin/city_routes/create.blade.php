@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ url('/admin?tab=miejskie') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-route me-2"></i>Dodaj nowy kurs miejski</h5>
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

                    <form action="{{ route('admin.city_routes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="carrier_id" class="form-label"><i class="fas fa-building me-2"></i>Przewoźnik <span class="text-danger">*</span></label>
                                <select class="form-select" id="carrier_id" name="carrier_id" required>
                                    <option value="">Wybierz przewoźnika</option>
                                    @foreach($carriers as $carrier)
                                        <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                                @error('carrier_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="line_id" class="form-label"><i class="fas fa-bus me-2"></i>Linia <span class="text-danger">*</span></label>
                                <select class="form-select" id="line_id" name="line_id" required>
                                    <option value="">Najpierw wybierz przewoźnika</option>
                                    @foreach($existingLines as $line)
                                        <option value="{{ $line->id }}" 
                                            data-carrier="{{ $line->carrier_id }}" 
                                            data-color="{{ $line->color }}" 
                                            data-name="{{ $line->name }}" 
                                            data-number="{{ $line->number }}" 
                                            class="line-option"
                                            {{ old('line_id') == $line->id ? 'selected' : '' }}
                                            style="display: none;"
                                        >
                                            Linia {{ $line->number }} ({{ $line->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('line_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label"><i class="fas fa-truck-moving me-2"></i>Pojazd <span class="text-danger">*</span></label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Najpierw wybierz przewoźnika</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-carrier="{{ $vehicle->line->carrier_id ?? '' }}" class="vehicle-option" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }} style="display: none;">
                                            {{ $vehicle->type }} {{ $vehicle->vehicle_number }} (Pojemność: {{ $vehicle->capacity ?? 'brak' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label"><i class="fas fa-map-signs me-2"></i>Nazwa trasy <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label"><i class="fas fa-city me-2"></i>Miasto <span class="text-danger">*</span></label>
                                <select class="form-select" id="city_id" name="city_id" required>
                                    <option value="">Wybierz miasto</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }} ({{ $city->voivodeship ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="travel_time" class="form-label"><i class="fas fa-clock me-2"></i>Czas przejazdu (minuty) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="travel_time" name="travel_time" min="1" value="{{ old('travel_time') }}" required>
                                @error('travel_time')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-calendar-week me-2"></i>Dni kursowania <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($daysOfWeek as $key => $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="day_{{ $key }}" name="days_of_week[]" value="{{ $key }}" {{ is_array(old('days_of_week')) && in_array($key, old('days_of_week')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day_{{ $key }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days_of_week')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktywny</label>
                                </div>
                                <small class="form-text text-muted">Nieaktywne kursy nie będą widoczne dla użytkowników.</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Po zapisaniu trasy miejskiej będziesz mógł dodać przystanki i godziny odjazdów w sekcji edycji.
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Zapisz trasę</button>
                                <a href="{{ route('admin.city_routes.index') }}" class="btn btn-secondary ms-2">Anuluj</a>
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
            // Elementy UI
            const carrierSelect = document.getElementById('carrier_id');
            const lineSelect = document.getElementById('line_id');
            const vehicleSelect = document.getElementById('vehicle_id');
            const nameInput = document.getElementById('name');
            const lineOptions = document.querySelectorAll('.line-option');
            const vehicleOptions = document.querySelectorAll('.vehicle-option');
            
            // Funkcja do filtrowania linii na podstawie wybranego przewoźnika
            function filterLines() {
                const selectedCarrierId = carrierSelect.value;
                let hasValidOptions = false;
                
                // Reset select
                lineSelect.value = '';
                
                if (!selectedCarrierId) {
                    // Jeśli nie wybrano przewoźnika, wyłącz dropdown linii
                    lineOptions.forEach(option => {
                        option.style.display = 'none';
                    });
                    lineSelect.querySelector('option:first-child').text = 'Najpierw wybierz przewoźnika';
                    lineSelect.disabled = true;
                } else {
                    // Włącz dropdown i pokaż tylko linie należące do wybranego przewoźnika
                    lineSelect.disabled = false;
                    lineOptions.forEach(option => {
                        const carrierId = option.getAttribute('data-carrier');
                        if (carrierId && carrierId === selectedCarrierId) {
                            option.style.display = '';
                            hasValidOptions = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });
                    lineSelect.querySelector('option:first-child').text = hasValidOptions ? 
                        'Wybierz linię' : 'Brak dostępnych linii dla tego przewoźnika';
                }
                
                // Uruchom również filtrowanie pojazdów
                filterVehicles();
            }
            
            // Funkcja do filtrowania pojazdów na podstawie wybranego przewoźnika
            function filterVehicles() {
                const selectedCarrierId = carrierSelect.value;
                let hasValidOptions = false;
                
                // Reset select
                vehicleSelect.value = '';
                
                if (!selectedCarrierId) {
                    // Jeśli nie wybrano przewoźnika, wyłącz dropdown pojazdów
                    vehicleOptions.forEach(option => {
                        option.style.display = 'none';
                    });
                    vehicleSelect.querySelector('option:first-child').text = 'Najpierw wybierz przewoźnika';
                    vehicleSelect.disabled = true;
                } else {
                    // Włącz dropdown i pokaż tylko pojazdy należące do wybranego przewoźnika
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
            
            // Obsługa wyboru linii
            lineSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const name = selectedOption.getAttribute('data-name');
                    
                    // Proponuj nazwę trasy bazując na nazwie linii
                    if (!nameInput.value) {
                        nameInput.value = name;
                    }
                }
            });
            
            // Inicjalizacja filtrów
            filterLines();
            filterVehicles();
            
            // Nasłuchiwanie zmiany przewoźnika
            carrierSelect.addEventListener('change', filterLines);

            // Sprawdź czy formularz jest poprawnie wypełniony przed wysłaniem
            document.querySelector('form').addEventListener('submit', function(e) {
                // Sprawdź czy wybrano linię
                if (!lineSelect.value) {
                    e.preventDefault();
                    alert('Proszę wybrać linię');
                    lineSelect.focus();
                    return;
                }
                
                // Sprawdź czy wybrano pojazd
                if (!vehicleSelect.value) {
                    e.preventDefault();
                    alert('Proszę wybrać pojazd');
                    vehicleSelect.focus();
                    return;
                }
            });
            
            // Wywołaj filtrowanie na starcie jeśli przewoźnik jest już wybrany
            if (carrierSelect.value) {
                filterLines();
            }
        });
    </script>
@endsection
