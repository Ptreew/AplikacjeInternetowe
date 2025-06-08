@extends('layouts.app')

@section('title', 'Kreator trasy - Krok 3')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.routes.builder.step2') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Powrót do kroku 2
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Kreator trasy - Krok 3 z 3: Harmonogram kursowania</h5>
                <span>3/3</span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Tworzenie harmonogramu dla trasy: {{ $routeData['basic_info']['name'] }}</h5>
                <p>Dodaj daty obowiązywania rozkładu, dni tygodnia i godziny odjazdów. Możesz utworzyć kilka różnych harmonogramów.</p>
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

            <!-- Podsumowanie trasy -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Podsumowanie trasy</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle me-2"></i>Podstawowe informacje</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-tag me-2"></i><strong>Nazwa trasy:</strong> {{ $routeData['basic_info']['name'] }}</li>
                                <li><i class="fas fa-map-signs me-2"></i><strong>Typ trasy:</strong> {{ $routeData['basic_info']['type'] == 'city' ? 'Miejska' : 'Międzymiastowa' }}</li>
                                <li><i class="fas fa-map-marker-alt me-2"></i><strong>Liczba przystanków:</strong> {{ count($stopsSequence) }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-bus me-2"></i>Przystanki</h6>
                            <ol class="mb-0">
                                @foreach($stopsSequence as $index => $stopData)
                                    <li>
                                        <strong>{{ $stopData['stop']->name }}</strong>
                                        @if($stopData['stop']->city)
                                            ({{ $stopData['stop']->city->name }})
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.routes.builder.step3.process') }}" method="POST" id="schedulesForm">
                @csrf
                
                <!-- Lista harmonogramów -->
                <div id="schedules-container">
                    <!-- Tu będą dodawane harmonogramy dynamicznie -->
                </div>
                
                <!-- Przycisk dodawania harmonogramów -->
                <div class="mb-4">
                    <button type="button" class="btn btn-success btn-sm" id="add-schedule">
                        <i class="fas fa-plus me-2"></i>Dodaj harmonogram
                    </button>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.routes.builder.cancel') }}" class="btn btn-secondary" id="cancel-button">
                        <i class="fas fa-times me-2"></i>Anuluj cały proces
                    </a>
                    
                    <div>
                        <a href="{{ route('admin.routes.builder.step2') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Wróć do przystanków
                        </a>
                        <button type="submit" class="btn btn-primary" id="finish-button">
                            <i class="fas fa-check me-2"></i>Zakończ i utwórz trasę
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Szablon dla harmonogramu -->
<template id="schedule-template">
    <div class="schedule-item card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Harmonogram <span class="schedule-number"></span></h6>
            <button type="button" class="btn btn-danger btn-sm remove-schedule">
                <i class="fas fa-trash-alt me-2"></i>Usuń
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Okres obowiązywania</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Od</span>
                            <input type="date" class="form-control valid-from" name="schedules[IDX][valid_from]" required>
                            <span class="input-group-text">do</span>
                            <input type="date" class="form-control valid-to" name="schedules[IDX][valid_to]" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dni tygodnia</label>
                    <div class="days-of-week">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-0-IDX" name="schedules[IDX][days_of_week][]" value="0">
                            <label class="form-check-label" for="day-0-IDX">Nd</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-1-IDX" name="schedules[IDX][days_of_week][]" value="1">
                            <label class="form-check-label" for="day-1-IDX">Pon</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-2-IDX" name="schedules[IDX][days_of_week][]" value="2">
                            <label class="form-check-label" for="day-2-IDX">Wt</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-3-IDX" name="schedules[IDX][days_of_week][]" value="3">
                            <label class="form-check-label" for="day-3-IDX">Śr</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-4-IDX" name="schedules[IDX][days_of_week][]" value="4">
                            <label class="form-check-label" for="day-4-IDX">Czw</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-5-IDX" name="schedules[IDX][days_of_week][]" value="5">
                            <label class="form-check-label" for="day-5-IDX">Pt</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="day-6-IDX" name="schedules[IDX][days_of_week][]" value="6">
                            <label class="form-check-label" for="day-6-IDX">Sob</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Godziny odjazdów</label>
                    <div class="departures-container">
                        <!-- Tu będą dodawane odjazdy dynamicznie -->
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm add-departure">
                            <i class="fas fa-plus me-2"></i>Dodaj odjazd
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Szablon dla odjazdu -->
<template id="departure-template">
    <div class="departure-item row mb-2">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">Godzina</span>
                <input type="time" class="form-control" name="schedules[SCHEDULE_IDX][departures][DEPARTURE_IDX][departure_time]" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">Pojazd</span>
                <select class="form-select" name="schedules[SCHEDULE_IDX][departures][DEPARTURE_IDX][vehicle_id]" required>
                    <option value="">Wybierz pojazd</option>
                    @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">
                        {{ $vehicle->registration_number }} ({{ $vehicle->type }}, {{ $vehicle->capacity }} miejsc)
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">Cena</span>
                <input type="number" step="0.01" min="0" class="form-control" name="schedules[SCHEDULE_IDX][departures][DEPARTURE_IDX][price]" value="0.00" required>
                <span class="input-group-text">zł</span>
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-departure w-100">
                <i class="fas fa-trash-alt me-2"></i>Usuń
            </button>
        </div>
    </div>
</template>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const schedulesContainer = document.getElementById('schedules-container');
    const addScheduleButton = document.getElementById('add-schedule');
    const scheduleTemplate = document.getElementById('schedule-template').content;
    const departureTemplate = document.getElementById('departure-template').content;
    const finishButton = document.getElementById('finish-button');
    const schedulesForm = document.getElementById('schedulesForm');
    const cancelButton = document.getElementById('cancel-button');
    
    // Funkcja czyszcząca localStorage
    function clearRouteBuilderStorage() {
        localStorage.removeItem('route_builder_city_stops');
        localStorage.removeItem('route_builder_intercity_stops');
        console.log('Wyczyszczono localStorage');
    }
    
    // Czyszczenie localStorage przy anulowaniu
    cancelButton.addEventListener('click', clearRouteBuilderStorage);
    
    // Czyszczenie localStorage przy zakończeniu tworzenia trasy
    finishButton.addEventListener('click', clearRouteBuilderStorage);
    
    let scheduleCounter = 0;
    
    // Dodaj pierwszy harmonogram domyślnie
    addSchedule();
    
    // Dodaj harmonogram
    addScheduleButton.addEventListener('click', addSchedule);
    
    // Walidacja i przesyłanie formularza
    schedulesForm.addEventListener('submit', function(event) {
        // Nie blokujemy domyślnego zachowania formularza
        
        // Dodanie DIV z informacją o wysyłaniu
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'alert alert-info';
        loadingDiv.innerHTML = '<strong>Wysyłanie danych...</strong> Proszę czekać.';
        schedulesForm.prepend(loadingDiv);
        
        // Wyświetl informacje debugowania
        console.log('%c FORMULARZ WYSYŁANY - DEBUGOWANIE', 'background: yellow; color: black; font-size: 16px');
        console.log('URL formularza:', schedulesForm.action);
        console.log('Metoda formularza:', schedulesForm.method);
        console.log('Liczba harmonogramów:', document.querySelectorAll('.schedule-item').length);
        
        // Sprawdźć czy CSRF token jest poprawny
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            console.log('CSRF token obecny:', csrfToken.value.substring(0, 10) + '...');
        } else {
            console.error('BRAK CSRF TOKEN!!');
            alert('Krytyczny błąd: brak tokena CSRF! Odśwież stronę.');
            return;
        }
        
        // Zbierz dane formularza do sprawdzenia
        const formData = new FormData(schedulesForm);
        console.log('Dane formularza:');
        const formDataObj = {};
        for (const [key, value] of formData.entries()) {
            formDataObj[key] = value;
            console.log(`${key} = ${value}`);
        }
        console.log('Wszystkie dane:', formDataObj);
        
        // Sprawdź walidację dat
        const isValid = validateDates();
        
        if (!isValid) {
            event.preventDefault(); // Zatrzymaj wysyłanie tylko gdy walidacja nie przechodzi
            console.error('Błąd walidacji dat');
            alert('Błąd: Data końcowa musi być późniejsza niż data początkowa dla każdego harmonogramu.');
            return false;
        }
        
        // Sprawdź czy są wybrane dni tygodnia
        const schedules = document.querySelectorAll('.schedule-item');
        console.log('Sprawdzanie dni tygodnia dla', schedules.length, 'harmonogramów');
        
        for (let i = 0; i < schedules.length; i++) {
            const daysCheckboxes = schedules[i].querySelectorAll('input[type="checkbox"]:checked');
            console.log('Harmonogram', i, 'zaznaczone dni:', daysCheckboxes.length);
            
            if (daysCheckboxes.length === 0) {
                event.preventDefault(); // Zatrzymaj wysyłanie tylko gdy walidacja nie przechodzi
                console.error('Brak zaznaczonych dni tygodnia');
                alert('Błąd: Musisz wybrać co najmniej jeden dzień tygodnia dla każdego harmonogramu.');
                return false;
            }
        }
        
        // Jeśli wszystko jest OK, wyświetl komunikat
        console.log('Formularz przechodzi pełną walidację - wysyłam dane na serwer standardową metodą');
        
        // Dodanie powiadomienia przed wysłaniem - opcjonalne
        // alert('Trwa zapisywanie danych. Proszę czekać...');
        
        console.log('Formularz przechodzi walidację - zostanie wysłany');        
        
        // Nie wyświetlamy alertu, tylko logujemy w konsoli
        console.log('Formularz zwalidowany poprawnie. Wysyłam dane...');
        
        // UWAGA! Rezygnujemy z Fetch API i wysyłamy formularz standardową metodą
        // Usunęliśmy preventDefault() powyżej, więc formularz zostanie wysłany standardową metodą
        return true; // Zezwól na standardowe wysłanie formularza
    });
    
    // Funkcja dodająca harmonogram
    function addSchedule() {
        const scheduleIndex = scheduleCounter++;
        
        // Sklonuj szablon
        const scheduleClone = document.importNode(scheduleTemplate, true);
        
        // Zaktualizuj indeksy i numery
        scheduleClone.querySelector('.schedule-number').textContent = scheduleIndex + 1;
        
        // Ustaw dzisiejszą datę jako domyślną datę początkową
        const today = new Date();
        const nextYear = new Date();
        nextYear.setFullYear(today.getFullYear() + 1);
        
        const validFromInput = scheduleClone.querySelector('.valid-from');
        const validToInput = scheduleClone.querySelector('.valid-to');
        
        validFromInput.value = formatDate(today);
        validToInput.value = formatDate(nextYear);
        
        // Zaktualizuj nazwy pól formularza
        updateFormFieldNames(scheduleClone, 'IDX', scheduleIndex);
        
        // Dodaj obsługę usuwania harmonogramu
        const removeButton = scheduleClone.querySelector('.remove-schedule');
        removeButton.addEventListener('click', function() {
            if (document.querySelectorAll('.schedule-item').length > 1) {
                this.closest('.schedule-item').remove();
                updateScheduleNumbers();
            } else {
                alert('Trasa musi mieć przynajmniej jeden harmonogram.');
            }
        });
        
        // Domyślnie zaznacz dni robocze
        const daysOfWeek = scheduleClone.querySelectorAll('.days-of-week input');
        daysOfWeek.forEach(checkbox => {
            const day = parseInt(checkbox.value);
            if (day >= 1 && day <= 5) { // Od poniedziałku do piątku
                checkbox.checked = true;
            }
        });
        
        // Dodaj obsługę dodawania odjazdów
        const addDepartureButton = scheduleClone.querySelector('.add-departure');
        const departuresContainer = scheduleClone.querySelector('.departures-container');
        
        addDepartureButton.addEventListener('click', function() {
            addDeparture(departuresContainer, scheduleIndex);
        });
        
        // Dodaj pierwszy odjazd domyślnie
        addDeparture(departuresContainer, scheduleIndex);
        
        // Dodaj cały harmonogram do kontenera
        schedulesContainer.appendChild(scheduleClone);
    }
    
    // Funkcja dodająca odjazd
    function addDeparture(container, scheduleIndex) {
        const departureIndex = container.children.length;
        
        // Sklonuj szablon
        const departureClone = document.importNode(departureTemplate, true);
        
        // Zaktualizuj nazwy pól formularza
        updateFormFieldNames(departureClone, 'SCHEDULE_IDX', scheduleIndex);
        updateFormFieldNames(departureClone, 'DEPARTURE_IDX', departureIndex);
        
        // Ustaw domyślną godzinę na poranną (7:00) + offset rosnący o 30 minut dla każdego kolejnego odjazdu
        const departureTime = departureClone.querySelector('input[type="time"]');
        const hours = Math.floor(7 + (departureIndex * 0.5));
        const minutes = (departureIndex % 2) * 30;
        departureTime.value = formatTime(hours, minutes);
        
        // Dodaj obsługę usuwania odjazdu
        const removeButton = departureClone.querySelector('.remove-departure');
        removeButton.addEventListener('click', function() {
            if (container.children.length > 1) {
                this.closest('.departure-item').remove();
                updateDepartureIndexes(container, scheduleIndex);
            } else {
                alert('Harmonogram musi mieć przynajmniej jeden odjazd.');
            }
        });
        
        // Dodaj odjazd do kontenera
        container.appendChild(departureClone);
    }
    
    // Aktualizuje indeksy w nazwach pól formularza
    function updateFormFieldNames(element, placeholder, index) {
        // Aktualizacja nazw pól formularza
        element.querySelectorAll('[name*="' + placeholder + '"]').forEach(field => {
            field.name = field.name.replace(placeholder, index);
        });
        
        // Aktualizacja identyfikatorów
        element.querySelectorAll('[id*="' + placeholder + '"]').forEach(field => {
            const newId = field.id.replace(placeholder, index);
            field.id = newId;
            
            // Aktualizuj powiązane etykiety
            const relatedLabel = element.querySelector(`label[for="${field.id}"]`);
            if (relatedLabel) {
                relatedLabel.htmlFor = newId;
            }
        });
    }
    
    // Aktualizuje numerację harmonogramów po usunięciu
    function updateScheduleNumbers() {
        document.querySelectorAll('.schedule-item').forEach((item, index) => {
            item.querySelector('.schedule-number').textContent = index + 1;
        });
    }
    
    // Aktualizuje indeksy odjazdów po usunięciu
    function updateDepartureIndexes(container, scheduleIndex) {
        Array.from(container.children).forEach((item, index) => {
            updateFormFieldNames(item, 'DEPARTURE_IDX', index);
        });
    }
    
    // Funkcja formatująca datę do formatu yyyy-mm-dd
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    // Funkcja do walidacji dat (końcowa > początkowa)
    function validateDates() {
        const schedules = document.querySelectorAll('.schedule-item');
        let isValid = true;
        
        schedules.forEach(schedule => {
            const fromDate = schedule.querySelector('.valid-from').value;
            const toDate = schedule.querySelector('.valid-to').value;
            
            if (fromDate && toDate) {
                const fromDateObj = new Date(fromDate);
                const toDateObj = new Date(toDate);
                
                if (fromDateObj >= toDateObj) {
                    isValid = false;
                    // Dodaj klasę błędu do pól
                    schedule.querySelector('.valid-from').classList.add('is-invalid');
                    schedule.querySelector('.valid-to').classList.add('is-invalid');
                } else {
                    // Usuń klasę błędu
                    schedule.querySelector('.valid-from').classList.remove('is-invalid');
                    schedule.querySelector('.valid-to').classList.remove('is-invalid');
                }
            }
        });
        
        return isValid;
    }
    
    // Formatuje czas do formatu hh:mm
    function formatTime(hours, minutes) {
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }
    
    // Koniec funkcji JavaScript
});
</script>
@endsection
@endsection
