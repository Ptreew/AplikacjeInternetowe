@extends('layouts.app')

@section('title', 'Kreator trasy - Krok 2')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.routes.builder.step1') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Powrót do kroku 1
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Kreator trasy międzymiastowej - Krok 2 z 3: Dodawanie przystanków</h5>
                <span>2/3</span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">66%</div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Tworzenie trasy międzymiastowej: {{ $routeData['basic_info']['name'] }}</h5>
                <p>Dodaj przystanki w kolejności ich występowania na trasie. <strong>Musisz wybrać co najmniej przystanek początkowy i końcowy</strong> (np. Kraków i Rzeszów), ale możesz też dodać przystanki pośrednie (np. Tarnów). Możesz sortować przystanki przeciągając i upuszczając wiersze.</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.routes.builder.step2.process') }}" method="POST" id="stopsForm">
                @csrf
                
                <div class="row mb-4">
                    <!-- Lista miast i przystanków -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <i class="fas fa-city me-2"></i>Miasta i przystanki
                            </div>
                            <div class="card-body p-0">
                                <div class="accordion" id="citiesAccordion" style="max-height: 500px; overflow-y: auto;">
                                    @foreach($cities as $city)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $city->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $city->id }}" aria-expanded="false" aria-controls="collapse{{ $city->id }}">
                                                <strong>{{ $city->name }}</strong> ({{ $city->stops->count() }} przystanków)
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $city->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $city->id }}" data-bs-parent="#citiesAccordion">
                                            <div class="accordion-body p-0">
                                                <div class="list-group list-group-flush">
                                                    @foreach($city->stops as $stop)
                                                    <a href="#" class="list-group-item list-group-item-action add-stop" 
                                                       data-id="{{ $stop->id }}" 
                                                       data-name="{{ $stop->name }}" 
                                                       data-city="{{ $city->name }}">
                                                        {{ $stop->name }}
                                                    </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista wybranych przystanków -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-map-marker-alt me-2"></i>Wybrane przystanki trasy</span>
                                <span class="badge bg-primary" id="stops-count">0</span>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0" id="selected-stops-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px">#</th>
                                            <th>Przystanek</th>
                                            <th>Miasto</th>
                                            <th>Czas do następnego (min)</th>
                                            <th>Odległość od początku (km)</th>
                                            <th style="width: 80px">Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected-stops">
                                        <tr class="text-center text-muted">
                                            <td colspan="6">Brak wybranych przystanków</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.routes.builder.cancel') }}" class="btn btn-secondary" id="cancel-button">
                        <i class="fas fa-times me-2"></i>Anuluj cały proces
                    </a>
                    
                    <div>
                        <a href="{{ route('admin.routes.builder.step1') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Wróć do kroku 1
                        </a>
                        <button type="submit" class="btn btn-primary" id="next-step-button" disabled>
                            Dalej: Harmonogram <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectedStopsTable = document.getElementById('selected-stops');
    const stopsForm = document.getElementById('stopsForm');
    const nextStepButton = document.getElementById('next-step-button');
    const stopsCounter = document.getElementById('stops-count');
    const cancelButton = document.getElementById('cancel-button');
    
    // Dodanie czyszczenia localStorage przy anulowaniu procesu
    cancelButton.addEventListener('click', function() {
        localStorage.removeItem('route_builder_city_stops');
        localStorage.removeItem('route_builder_intercity_stops');
        console.log('Wyczyszczono localStorage po anulowaniu buildera');
    });
    
    let selectedStops = [];
    
    // Klucz localStorage dla przystanków międzymiastowych
    const LOCAL_STORAGE_KEY = 'route_builder_intercity_stops';
    
    // Sprawdzenie, czy to było pełne odświeżenie (Ctrl+F5)
    const isForcedRefresh = localStorage.getItem('force_refresh_check') === null;
    localStorage.setItem('force_refresh_check', new Date().getTime());
    
    // Funkcja zapisująca stan przystanków do localStorage
    function saveStopsToLocalStorage() {
        const dataToSave = {
            stops: selectedStops,
            timestamp: new Date().getTime()
        };
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(dataToSave));
    }
    
    // Funkcja wczytująca zapisane przystanki z localStorage
    function loadStopsFromLocalStorage() {
        if (isForcedRefresh) {
            console.log('Pełne odświeżenie strony (Ctrl+F5) - czyszczenie zapisanych przystanków');
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            return;
        }
        
        const savedData = localStorage.getItem(LOCAL_STORAGE_KEY);
        if (!savedData) return;
        
        const data = JSON.parse(savedData);
        
        // Sprawdź czy dane nie są zbyt stare (np. starsze niż 1 godzina)
        const oneHourInMs = 60 * 60 * 1000;
        if (new Date().getTime() - data.timestamp > oneHourInMs) {
            console.log('Zapisane dane są zbyt stare - usuwanie');
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            return;
        }
        
        // Dodaj zapisane przystanki
        if (data.stops && data.stops.length) {
            data.stops.forEach(stop => {
                // Dodajemy przystanki bezpośrednio bez potrzeby pobierania danych z serwera
                addStop(stop);
            });
        }
    }
    
    // Wczytaj zapisane przystanki przy starcie
    loadStopsFromLocalStorage();
    
    // Enable sorting of selected stops
    new Sortable(selectedStopsTable, {
        animation: 150,
        ghostClass: 'bg-light',
        onEnd: function() {
            updateStopNumbers();
        }
    });
    
    // Add event listeners for add stop buttons
    document.querySelectorAll('.add-stop').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const stopId = parseInt(this.dataset.id);
            const stopName = this.dataset.name;
            const cityName = this.dataset.city;
            
            // Check if already added
            if (selectedStops.some(stop => stop.id === stopId)) {
                alert('Ten przystanek jest już dodany do trasy.');
                return;
            }
            
            addStop({
                id: stopId,
                name: stopName,
                city: cityName
            });
        });
    });
    
    // Add a stop to selected stops
    function addStop(stop) {
        selectedStops.push(stop);
        
        // Zapisz zmiany do localStorage
        saveStopsToLocalStorage();
        
        if (selectedStops.length === 1) {
            // Clear the placeholder text
            selectedStopsTable.innerHTML = '';
        }
        
        const row = document.createElement('tr');
        row.dataset.id = stop.id;
        row.innerHTML = `
            <td class="stop-number">${selectedStops.length}</td>
            <td>${stop.name}</td>
            <td>${stop.city}</td>
            <td>
                <input type="hidden" name="stop_ids[]" value="${stop.id}">
                <input type="number" class="form-control form-control-sm time-to-next" name="time_to_next[]" 
                       value="${selectedStops.length === 1 ? '0' : '20'}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm distance-from-start" name="distance_from_start[]" 
                       step="0.1" min="0" value="${selectedStops.length === 1 ? '0' : ''}">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-stop">
                    <i class="fas fa-trash"></i> Usuń
                </button>
            </td>
        `;
        
        // Add event listener for remove button
        const removeButton = row.querySelector('.remove-stop');
        removeButton.addEventListener('click', function() {
            const stopId = parseInt(row.dataset.id);
            selectedStops = selectedStops.filter(s => s.id !== stopId);
            row.remove();
            
            // Zapisz zmiany do localStorage po usunięciu przystanku
            saveStopsToLocalStorage();
            
            // If all stops are removed, show placeholder
            if (selectedStops.length === 0) {
                selectedStopsTable.innerHTML = `
                    <tr class="text-center text-muted">
                        <td colspan="6">Brak wybranych przystanków</td>
                    </tr>
                `;
            } else {
                updateStopNumbers();
            }
            
            updateStopsCount();
            checkFormValidity();
        });
        
        selectedStopsTable.appendChild(row);
        updateStopsCount();
        checkFormValidity();
    }
    
    // Update stop numbers after drag & drop
    function updateStopNumbers() {
        const rows = selectedStopsTable.querySelectorAll('tr:not(.text-center)');
        rows.forEach((row, index) => {
            row.querySelector('.stop-number').textContent = index + 1;
            
            const timeInput = row.querySelector('.time-to-next');
            const distanceInput = row.querySelector('.distance-from-start');
            
            // Handle first stop
            if (index === 0) {
                // First stop zawsze ma odległość 0 od początku
                distanceInput.value = '0';
                distanceInput.readOnly = true;
                
                // Pierwszy przystanek może mieć czas do następnego
                timeInput.readOnly = false;
                timeInput.min = '0';
                if (!timeInput.value) {
                    timeInput.value = '20';
                }
            } 
            // Handle last stop
            else if (index === rows.length - 1) {
                // Last stop może mieć czas do następnego równy 0
                if (!timeInput.value) {
                    timeInput.value = '0';
                }
                timeInput.min = '0';
                timeInput.readOnly = false;
                
                // Last stop has editable distance
                distanceInput.readOnly = false;
            }
            // Handle middle stops
            else {
                // Middle stops have editable time
                timeInput.readOnly = false;
                timeInput.min = '0';
                if (!timeInput.value) {
                    timeInput.value = '20';
                }
                
                // Middle stops have editable distance
                distanceInput.readOnly = false;
            }
        });
        
        // Obliczenie łącznego czasu podróży
        calculateTotalTravelTime();
    }
    
    // Obliczanie łącznego czasu podróży
    function calculateTotalTravelTime() {
        const timeInputs = document.querySelectorAll('.time-to-next');
        let totalTime = 0;
        
        timeInputs.forEach((input) => {
            const time = parseInt(input.value) || 0;
            totalTime += time;
        });
        
        // Zapisanie łącznego czasu podróży w ukrytym polu
        let hiddenTravelTimeInput = document.getElementById('calculated_travel_time');
        if (!hiddenTravelTimeInput) {
            hiddenTravelTimeInput = document.createElement('input');
            hiddenTravelTimeInput.type = 'hidden';
            hiddenTravelTimeInput.name = 'calculated_travel_time';
            hiddenTravelTimeInput.id = 'calculated_travel_time';
            stopsForm.appendChild(hiddenTravelTimeInput);
        }
        hiddenTravelTimeInput.value = totalTime;
        console.log('Obliczony łączny czas podróży:', totalTime);
    }
    
    // Update the stops counter
    function updateStopsCount() {
        stopsCounter.textContent = selectedStops.length;
    }
    
    // Check if form can be submitted
    function checkFormValidity() {
        nextStepButton.disabled = selectedStops.length < 2;
    }
    
    // Submit form handler
    stopsForm.addEventListener('submit', function(e) {
        if (selectedStops.length < 2) {
            e.preventDefault();
            alert('Trasa musi zawierać co najmniej 2 przystanki.');
        }
    });
});
</script>
@endsection
@endsection
