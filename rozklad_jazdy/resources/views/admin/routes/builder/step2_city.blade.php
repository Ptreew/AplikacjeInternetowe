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
                <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Kreator trasy miejskiej - Krok 2 z 3: Dodawanie przystanków</h5>
                <span>2/3</span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">66%</div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Tworzenie trasy miejskiej: {{ $routeData['basic_info']['name'] }}</h5>
                <p>Dodaj przystanki w kolejności ich występowania na trasie. <strong>Musisz wybrać co najmniej przystanek początkowy i końcowy</strong> (np. pierwsza i ostatnia pętla lub dworzec). Możesz sortować przystanki przeciągając i upuszczając wiersze.</p>
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
                
                <!-- Wybór miasta -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="city_select" class="form-label"><i class="fas fa-city me-2"></i>Wybierz miasto</label>
                        <select class="form-select" id="city_select">
                            <option value="">-- Wybierz miasto --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="stop_search" class="form-label"><i class="fas fa-search me-2"></i>Wyszukaj przystanek</label>
                        <input type="text" class="form-control" id="stop_search" placeholder="Wpisz nazwę przystanku">
                    </div>
                </div>
                
                <!-- Lista dostępnych przystanków -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <i class="fas fa-list me-2"></i>Dostępne przystanki
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group available-stops" id="available-stops" style="max-height: 300px; overflow-y: auto;">
                                    <div class="list-group-item text-center text-muted">
                                        Wybierz miasto, aby zobaczyć dostępne przystanki
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-map-marker-alt me-2"></i>Wybrane przystanki</span>
                                <span class="badge bg-primary" id="stops-count">0</span>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0" id="selected-stops-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px">#</th>
                                            <th>Przystanek</th>
                                            <th>Czas do następnego (min)</th>
                                            <th>Odległość od początku (km)</th>
                                            <th style="width: 80px">Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected-stops">
                                        <tr class="text-center text-muted">
                                            <td colspan="5">Brak wybranych przystanków</td>
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
    const citySelect = document.getElementById('city_select');
    const stopSearch = document.getElementById('stop_search');
    const availableStops = document.getElementById('available-stops');
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
    
    let stops = [];
    let selectedStops = [];
    let counter = 0;
    
    // Klucz localStorage dla przystanków miejskich
    const LOCAL_STORAGE_KEY = 'route_builder_city_stops';
    
    // Sprawdzenie, czy to było pełne odświeżenie (Ctrl+F5)
    const isForcedRefresh = localStorage.getItem('force_refresh_check') === null;
    localStorage.setItem('force_refresh_check', new Date().getTime());
    
    // Funkcja zapisująca stan przystanków do localStorage
    function saveStopsToLocalStorage() {
        const dataToSave = {
            cityId: citySelect.value,
            stops: selectedStops,
            timestamp: new Date().getTime()
        };
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(dataToSave));
    }
    
    // Funkcja wczytująca zapisane przystanki z localStorage
    async function loadStopsFromLocalStorage() {
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
        
        // Wybierz miasto z zapisanych danych
        if (data.cityId) {
            citySelect.value = data.cityId;
            
            // Załaduj przystanki dla wybranego miasta
            try {
                const response = await fetch(`/admin/routes/builder/stops-by-city/${data.cityId}`);
                stops = await response.json();
                renderAvailableStops(stops);
                
                // Dodaj zapisane przystanki
                if (data.stops && data.stops.length) {
                    data.stops.forEach(stop => addStop(stop));
                }
            } catch (error) {
                console.error('Błąd podczas wczytywania przystanków:', error);
            }
        }
    }
    
    // Enable sorting of selected stops
    new Sortable(selectedStopsTable, {
        animation: 150,
        ghostClass: 'bg-light',
        onEnd: function() {
            updateStopNumbers();
        }
    });
    
    // Load stops for selected city
    citySelect.addEventListener('change', function() {
        const cityId = this.value;
        if (!cityId) {
            availableStops.innerHTML = '<div class="list-group-item text-center text-muted">Wybierz miasto, aby zobaczyć dostępne przystanki</div>';
            return;
        }
        
        fetch(`/admin/routes/builder/stops-by-city/${cityId}`)
            .then(response => response.json())
            .then(data => {
                stops = data;
                renderAvailableStops(data);
            })
            .catch(error => console.error('Error fetching stops:', error));
    });
    
    // Filter stops as user types
    stopSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (!stops.length) return;
        
        const filteredStops = stops.filter(stop => 
            stop.name.toLowerCase().includes(searchTerm)
        );
        
        renderAvailableStops(filteredStops);
    });
    
    // Render available stops
    function renderAvailableStops(stopsData) {
        if (!stopsData.length) {
            availableStops.innerHTML = '<div class="list-group-item text-center text-muted">Brak przystanków</div>';
            return;
        }
        
        availableStops.innerHTML = '';
        
        stopsData.forEach(stop => {
            // Skip stops that are already selected
            if (selectedStops.some(s => s.id === stop.id)) {
                return;
            }
            
            const item = document.createElement('a');
            item.href = "#";
            item.className = "list-group-item list-group-item-action";
            item.textContent = stop.name;
            item.dataset.id = stop.id;
            item.dataset.name = stop.name;
            
            item.addEventListener('click', function(e) {
                e.preventDefault();
                addStop(stop);
                this.remove(); // Remove from available list
            });
            
            availableStops.appendChild(item);
        });
    }
    
    // Wczytaj zapisane przystanki przy starcie
    loadStopsFromLocalStorage();
    
    // Add a stop to selected stops
    function addStop(stop) {
        if (selectedStops.some(s => s.id === stop.id)) {
            return; // Already added
        }
        
        selectedStops.push(stop);
        counter++;
        
        // Zapisz do localStorage po dodaniu przystanku
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
            <td>
                <input type="hidden" name="stop_ids[]" value="${stop.id}">
                <input type="number" class="form-control form-control-sm" name="time_to_next[]" 
                       min="0"
                       value="5">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" name="distance_from_start[]" 
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
            const stopId = row.dataset.id;
            selectedStops = selectedStops.filter(s => s.id !== parseInt(stopId));
            row.remove();
            updateStopNumbers();
            
            // Zapisz zmiany do localStorage po usunięciu przystanku
            saveStopsToLocalStorage();
            
            // If all stops are removed, show placeholder
            if (selectedStops.length === 0) {
                selectedStopsTable.innerHTML = `
                    <tr class="text-center text-muted">
                        <td colspan="5">Brak wybranych przystanków</td>
                    </tr>
                `;
            }
            
            // Return stop to available list if city is still selected
            const cityId = citySelect.value;
            if (cityId) {
                const stopToReturn = stops.find(s => s.id === parseInt(stopId));
                if (stopToReturn) {
                    const item = document.createElement('a');
                    item.href = "#";
                    item.className = "list-group-item list-group-item-action";
                    item.textContent = stopToReturn.name;
                    item.dataset.id = stopToReturn.id;
                    item.dataset.name = stopToReturn.name;
                    
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        addStop(stopToReturn);
                        this.remove();
                    });
                    
                    availableStops.appendChild(item);
                }
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
            
            // Wszystkie przystanki mogą mieć czas do następnego
            const timeInput = row.querySelector('input[name="time_to_next[]"]');
            if (!timeInput.value && index < rows.length - 1) {
                // Dodaj domyślną wartość dla przystanków bez czasu
                timeInput.value = '5';
            }
            
            // Obliczanie łącznego czasu podróży
            calculateTotalTravelTime();
        });
    }
    
    // Obliczanie łącznego czasu podróży
    function calculateTotalTravelTime() {
        const timeInputs = document.querySelectorAll('input[name="time_to_next[]"]');
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
