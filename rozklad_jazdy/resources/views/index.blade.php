@extends('layouts.app')

@section('title', 'Rozkład jazdy')

{{-- Navigation is handled in the main layout --}}

@section('content')
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="mb-4">Wyszukaj połączenie</h2>
                
                <div class="tabs-container mb-4">
                    <div class="tabs">
                        <button class="tab-button active btn btn-primary" data-tab="miedzymiastowe">Międzymiastowe</button>
                        <button class="tab-button btn btn-outline-secondary" data-tab="miejskie">Miejskie</button>
                    </div>
                </div>
                
                <div class="card shadow">
                    <div class="card-body">

                        <!-- Międzymiastowe -->
                        <section class="tab-content" id="miedzymiastowe">
                            <h4>Wyszukaj kurs międzymiastowy</h4>
                            <form action="{{ route('routes.search.results') }}" method="POST" class="route-search-form">
                                @csrf
                                <!-- Dropdowns at the top -->
                                <div class="dropdowns-container">
                                    <select name="from_city" id="from_city" required>
                                        <option value="">Z (miasto początkowe)</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select name="to_city" id="to_city" required>
                                        <option value="">Do (miasto docelowe)</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select name="transport_type" id="transport_type">
                                        <option value="">Wszystkie środki transportu</option>
                                        @foreach($vehicleTypes as $type)
                                            <option value="{{ $type }}" {{ request('transport_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Date and time fields side by side at the bottom -->
                                <div class="datetime-container">
                                    <input type="date" id="date" name="date" placeholder="Data">
                                    <input type="time" id="time_from" name="time_from" pattern="[0-9]{2}:[0-9]{2}" step="60" placeholder="Od godziny">
                                    <input type="time" id="time_to" name="time_to" pattern="[0-9]{2}:[0-9]{2}" step="60" placeholder="Do godziny">
                                    <button type="submit">Szukaj połączeń</button>
                                </div>
                            </form>
                        </section>

                        <!-- Miejskie -->
                        <section class="tab-content hidden" id="miejskie">
                            <h4>Wyszukaj kurs miejski</h4>
                            <form action="{{ route('routes.search.city') }}" method="POST" class="city-route-search-form">
                                @csrf
                                <!-- Dropdowns at the top -->
                                <div class="dropdowns-container">
                                    <select name="city_id" id="city_id" required>
                                        <option value="">Wybierz miasto</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select name="from_stop" id="from_stop" required>
                                        <option value="">Z (przystanek początkowy)</option>
                                        @if(isset($stops) && $stops->count() > 0)
                                            @foreach($stops as $stop)
                                                <option value="{{ $stop->id }}">{{ $stop->name }}</option>
                                            @endforeach
                                        @endif
                                        <!-- This will be populated with AJAX based on selected city -->
                                    </select>
                                    
                                    <select name="to_stop" id="to_stop" required>
                                        <option value="">Do (przystanek końcowy)</option>
                                        @if(isset($stops) && $stops->count() > 0)
                                            @foreach($stops as $stop)
                                                <option value="{{ $stop->id }}">{{ $stop->name }}</option>
                                            @endforeach
                                        @endif
                                        <!-- This will be populated with AJAX based on selected city -->
                                    </select>
                                    
                                    <select name="transport_type" id="transport_type_city">
                                        <option value="">Wszystkie środki transportu</option>
                                        @foreach($vehicleTypes as $type)
                                            <option value="{{ $type }}" {{ request('transport_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Date and time fields side by side at the bottom -->
                                <div class="datetime-container">
                                    <input type="date" id="city_date" name="date" placeholder="Data">
                                    <input type="time" id="city_time_from" name="time_from" pattern="[0-9]{2}:[0-9]{2}" step="60" placeholder="Od godziny">
                                    <input type="time" id="city_time_to" name="time_to" pattern="[0-9]{2}:[0-9]{2}" step="60" placeholder="Do godziny">
                                    <button type="submit">Szukaj połączeń</button>
                                </div>
                            </form>
                        </section>
                        
                        <!-- Wyniki wyszukiwania -->
                        <div class="search-results mt-5">
                            @if(session('error'))
                                <div class="alert alert-danger mb-4">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if(isset($routesPaginator) && count($routesPaginator) > 0)
                                <h4>Znalezione połączenia</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Linia</th>
                                                <th>Przewoźnik</th>
                                                <th>Trasa</th>
                                                <th>Odjazd</th>
                                                <th>Przyjazd</th>
                                                <th>Dzień</th>
                                                <th>Pojazd</th>
                                                <th>Akcje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($routesPaginator as $route)
                                                @if(isset($route->schedules) && count($route->schedules) > 0)
                                                    @foreach($route->schedules as $schedule)
                                                        @if(isset($schedule->departures) && count($schedule->departures) > 0)
                                                            @foreach($schedule->departures as $departure)
                                                                <tr>
                                                                    <td class="align-middle">{{ $route->line->name ?? 'Brak' }}</td>
                                                                    <td class="align-middle">{{ $route->line->carrier->name ?? 'Brak' }}</td>
                                                                    <td class="align-middle">
                                                                        @if(isset($fromCity) && isset($toCity))
                                                                            {{ $fromCity->name }} → {{ $toCity->name }}
                                                                        @elseif(isset($fromStop) && isset($toStop))
                                                                            {{ $fromStop->name }} → {{ $toStop->name }}
                                                                        @else
                                                                            Brak danych o trasie
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle">{{ $departure->departure_time }}</td>
                                                                    <td class="align-middle">{{ $departure->arrival_time ?? 'Brak danych' }}</td>
                                                                    <td class="align-middle">
                                                                        @switch($schedule->day_type)
                                                                            @case('weekday')
                                                                                Dzień roboczy
                                                                                @break
                                                                            @case('saturday')
                                                                                Sobota
                                                                                @break
                                                                            @case('sunday')
                                                                                Niedziela
                                                                                @break
                                                                            @default
                                                                                {{ $schedule->day_type }}
                                                                        @endswitch
                                                                    </td>
                                                                    <td class="align-middle">{{ $departure->vehicle->type ?? 'Brak' }}</td>
                                                                    <td class="align-middle" style="white-space: nowrap;">
                                                                        <a href="{{ route('routes.show', ['route' => $route->id]) }}" class="btn btn-sm btn-warning">Szczegóły</a>
                                                                        @if(auth()->check())
                                                                            <a href="{{ route('tickets.create', ['departure_id' => $departure->id]) }}" class="btn btn-sm btn-success">Kup Bilet</a>
                                                                        @else
                                                                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Kup bilet</a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Paginacja -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $routesPaginator->appends(request()->except('page'))->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add form validation for the międzymiastowe search form
            const fromCity = document.getElementById('from_city');
            const toCity = document.getElementById('to_city');
            
            if (fromCity && toCity) {
                // Set up validation on change for intercity form
                fromCity.addEventListener('change', validateCities);
                toCity.addEventListener('change', validateCities);
                
                // Validate before form submission
                const searchForm = document.querySelector('.route-search-form');
                if (searchForm) {
                    searchForm.addEventListener('submit', function(e) {
                        if (fromCity.value && toCity.value && fromCity.value === toCity.value) {
                            e.preventDefault();
                            toCity.setCustomValidity('Miasto początkowe i docelowe nie mogą być takie same. Wybierz różne miasta.');
                            toCity.reportValidity();
                            return false;
                        }
                    });
                }
            }
            
            function validateCities() {
                if (fromCity.value && toCity.value && fromCity.value === toCity.value) {
                    toCity.setCustomValidity('Miasto początkowe i docelowe nie mogą być takie same. Wybierz różne miasta.');
                } else {
                    toCity.setCustomValidity('');
                }
            }
            
            // Add form validation for the miejskie search form - use existing variables defined below
            
            // Validate before form submission - we'll use the fromStopSelect and toStopSelect variables
            // that are defined later in the code
            const citySearchForm = document.querySelector('.city-route-search-form');
            if (citySearchForm) {
                citySearchForm.addEventListener('submit', function(e) {
                    if (fromStopSelect && toStopSelect && 
                        fromStopSelect.value && toStopSelect.value && 
                        fromStopSelect.value === toStopSelect.value) {
                        e.preventDefault();
                        toStopSelect.setCustomValidity('Przystanek początkowy i docelowy nie mogą być takie same. Wybierz różne przystanki.');
                        toStopSelect.reportValidity();
                        return false;
                    }
                });
            }
            // Set default date and time values
            const now = new Date();
            const dateStr = now.toISOString().slice(0, 10);
            const currentHour = String(now.getHours()).padStart(2, '0');
            const currentMinutes = String(now.getMinutes()).padStart(2, '0');
            
            // Domyślne wartości czasu (od bieżącej godziny do +3h później)
            const timeFrom = `${currentHour}:${currentMinutes}`;
            
            // Oblicz godzinę +3h później
            const laterTime = new Date(now);
            laterTime.setHours(laterTime.getHours() + 3);
            const laterHour = String(laterTime.getHours()).padStart(2, '0');
            const laterMinutes = String(laterTime.getMinutes()).padStart(2, '0');
            const timeTo = `${laterHour}:${laterMinutes}`;
            
            // Ustaw wartości dla formularza międzymiastowego
            document.getElementById('date').value = dateStr;
            document.getElementById('time_from').value = timeFrom;
            document.getElementById('time_to').value = timeTo;
            
            // Ustaw wartości dla formularza miejskiego
            document.getElementById('city_date').value = dateStr;
            document.getElementById('city_time_from').value = timeFrom;
            document.getElementById('city_time_to').value = timeTo;
            
            // Ustaw aktywną zakładkę na podstawie odpowiedzi serwera
            const activeTab = '{{ $activeTab ?? "miedzymiastowe" }}';
            setActiveTab(activeTab);

            // Dodaj style dla nowych kontenerów
            const style = document.createElement('style');
            style.textContent = `
                .route-search-form, .city-route-search-form {
                    width: 100%;
                }
                .dropdowns-container, .datetime-container {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    margin-bottom: 15px;
                    width: 100%;
                }
                .dropdowns-container select {
                    width: 100%;
                    display: block;
                }
                .datetime-container {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    width: 100%;
                }
                .datetime-container input {
                    flex: 1;
                    min-width: 120px;
                }
                .datetime-container button {
                    flex: 0 0 auto;
                    min-width: 100px;
                    margin-left: auto;
                }
            `;
            document.head.appendChild(style);
        });
        
        // Funkcja przełączania zakładek
        const tabButtons = document.querySelectorAll('.tab-button');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                setActiveTab(tabId);
            });
        });
        
        function setActiveTab(tabId) {
            // Ukryj wszystkie zakładki
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Usuń klasy active ze wszystkich przycisków i zmień styl
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-secondary');
            });
            
            // Pokaż wybraną zakładkę
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }
            
            // Dodaj klasę active do klikniętego przycisku
            const activeButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
            if (activeButton) {
                activeButton.classList.add('active');
                activeButton.classList.remove('btn-outline-secondary');
                activeButton.classList.add('btn-primary');
            }
        }
        
        // Funkcja do filtrowania przystanków według miasta
        const citySelect = document.getElementById('city_id');
        const fromStopSelect = document.getElementById('from_stop');
        const toStopSelect = document.getElementById('to_stop');
        
        // Funkcja do aktualizacji przystanków na podstawie wybranego miasta
        function updateStops() {
            const cityId = citySelect.value;
            
            if (!cityId) {
                // Jeśli miasto nie jest wybrane, wyczyść listy przystanków
                clearStopOptions(fromStopSelect);
                clearStopOptions(toStopSelect);
                return;
            }
            
            // Pobierz przystanki dla wybranego miasta za pomocą AJAX
            fetch(`/stops/by-city/${cityId}`)
                .then(response => response.json())
                .then(data => {
                    // Zaktualizuj obie listy przystanków
                    updateStopOptions(fromStopSelect, data);
                    updateStopOptions(toStopSelect, data);
                })
                .catch(error => console.error('Błąd pobierania przystanków:', error));
        }
        
        // Funkcja do czyszczenia opcji w liście przystanków
        function clearStopOptions(selectElement) {
            // Zachowaj tylko pierwszą opcję (placeholder)
            while (selectElement.options.length > 1) {
                selectElement.remove(1);
            }
        }
        
        // Funkcja do aktualizacji opcji w liście przystanków
        function updateStopOptions(selectElement, stops) {
            // Wyczyść obecne opcje (oprócz placeholdera)
            clearStopOptions(selectElement);
            
            // Dodaj nowe opcje na podstawie pobranych danych
            stops.forEach(stop => {
                const option = document.createElement('option');
                option.value = stop.id;
                option.textContent = stop.name;
                selectElement.appendChild(option);
            });
        }
        
        // Dodaj nasłuchiwacz zdarzeń do listy rozwijanej miast
        if (citySelect) {
            citySelect.addEventListener('change', updateStops);
        }
        
        // Initialize the validation for stops once they are loaded
        if (fromStopSelect && toStopSelect) {
            fromStopSelect.addEventListener('change', validateStops);
            toStopSelect.addEventListener('change', validateStops);
        }
        
        // Function to validate stops are different
        function validateStops() {
            if (fromStopSelect.value && toStopSelect.value && 
                fromStopSelect.value === toStopSelect.value) {
                toStopSelect.setCustomValidity('Przystanek początkowy i docelowy nie mogą być takie same. Wybierz różne przystanki.');
            } else {
                toStopSelect.setCustomValidity('');
            }
        }
    </script>
@endsection
