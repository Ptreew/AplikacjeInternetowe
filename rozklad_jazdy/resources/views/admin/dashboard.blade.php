@extends('layouts.app')

@section('title', 'Statystyki')

@section('header', 'Statystyki')

{{-- Navigation is handled in the main layout --}}

@section('content')
    @php
        // Check URL parameter 'tab'
        $tabFromUrl = request()->query('tab');
        
        // Set default active tab
        $finalActiveTab = 'miedzymiastowe'; // default value
        
        if (in_array($tabFromUrl, ['miedzymiastowe', 'miejskie', 'uzytkownicy', 'pojazdy', 'przewoznicy', 'lines', 'routes', 'cities', 'stops', 'schedules', 'departures', 'tickets'])) {
            $finalActiveTab = $tabFromUrl;
        } elseif (isset($activeTab) && in_array($activeTab, ['miedzymiastowe', 'miejskie', 'uzytkownicy', 'pojazdy', 'przewoznicy', 'lines', 'routes', 'cities', 'stops', 'schedules', 'departures', 'tickets'])) {
            $finalActiveTab = $activeTab;
        }
    @endphp

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h3 class="card-title">Użytkownicy</h3>
                    <div class="display-4 my-3">{{ $users }}</div>
                    <button class="tab-button btn btn-outline-primary" data-tab="uzytkownicy">Zarządzaj użytkownikami</button>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h3 class="card-title">Przewoźnicy</h3>
                    <div class="display-4 my-3">{{ $carriers }}</div>
                    <button class="tab-button btn btn-outline-primary" data-tab="przewoznicy">Zarządzaj przewoźnikami</button>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h3 class="card-title">Linie</h3>
                    <div class="display-4 my-3">{{ $lines }}</div>
                    <button class="tab-button btn btn-outline-primary" data-tab="lines">Zarządzaj liniami</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tabs-container mb-4">
        <div class="tabs">
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'miedzymiastowe' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="miedzymiastowe">Międzymiastowe</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'miejskie' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="miejskie">Miejskie</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'uzytkownicy' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="uzytkownicy">Użytkownicy</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'pojazdy' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="pojazdy">Pojazdy</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'przewoznicy' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="przewoznicy">Przewoźnicy</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'lines' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="lines">Linie</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'routes' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="routes">Trasy</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'cities' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="cities">Miasta</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'stops' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="stops">Przystanki</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'schedules' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="schedules">Rozkłady</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'departures' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="departures">Odjazdy</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'tickets' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="tickets">Bilety</button>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Szybkie akcje</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin') }}" class="btn btn-primary">Panel administratora</a>
                <button class="btn btn-primary" id="backup-button">Wykonaj kopię bazy danych</button>
                <button class="btn btn-primary" id="logs-button">Przeglądaj logi</button>
            </div>
        </div>
    </div>
    
    <div class="tab-content">
        <!-- Międzymiastowe -->
        <div class="tab-pane {{ $finalActiveTab == 'miedzymiastowe' ? 'show active' : '' }}" id="miedzymiastowe">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie kursami międzymiastowymi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> W tej sekcji możesz zarządzać trasami, liniami i kursami międzymiastowymi.
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Dodaj nowy kurs
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Z</th>
                                    <th>Do</th>
                                    <th>Przewoźnik</th>
                                    <th>Aktywny</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Warszawa</td>
                                    <td>Kraków</td>
                                    <td>PKP Intercity</td>
                                    <td><span class="badge bg-success">Tak</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                            <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Miejskie -->
        <div class="tab-pane {{ $finalActiveTab == 'miejskie' ? 'show active' : '' }}" id="miejskie">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie liniami miejskimi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> W tej sekcji możesz zarządzać liniami miejskimi, przystankami i rozkładami.
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Dodaj nową linię miejską
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Numer linii</th>
                                    <th>Miasto</th>
                                    <th>Typ</th>
                                    <th>Liczba przystanków</th>
                                    <th>Aktywna</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>175</td>
                                    <td>Warszawa</td>
                                    <td>Autobus</td>
                                    <td>15</td>
                                    <td><span class="badge bg-success">Tak</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                            <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Użytkownicy -->
        <div class="tab-pane {{ $finalActiveTab == 'uzytkownicy' ? 'show active' : '' }}" id="uzytkownicy">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie użytkownikami</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> W tej sekcji możesz zarządzać kontami użytkowników, rolami i uprawnieniami.
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Dodaj nowego użytkownika
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Imię i nazwisko</th>
                                    <th>Email</th>
                                    <th>Rola</th>
                                    <th>Data rejestracji</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ Auth::user()->name }}</td>
                                    <td>{{ Auth::user()->email }}</td>
                                    <td><span class="badge bg-primary">Administrator</span></td>
                                    <td>{{ now()->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                            <button class="btn btn-sm btn-outline-danger">Zablokuj</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pojazdy -->
        <div class="tab-pane {{ $finalActiveTab == 'pojazdy' ? 'show active' : '' }}" id="pojazdy">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie pojazdami</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> W tej sekcji możesz zarządzać pojazdami, ich typami i przypisaniem do linii.
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Dodaj nowy pojazd
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Numer</th>
                                    <th>Typ</th>
                                    <th>Linia</th>
                                    <th>Pojemność</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>EP09-042</td>
                                    <td>Pociąg ekspresowy</td>
                                    <td>Warszawa-Kraków</td>
                                    <td>350</td>
                                    <td><span class="badge bg-success">Aktywny</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                            <button class="btn btn-sm btn-outline-danger">Dezaktywuj</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Przewoźnicy -->
        <div class="tab-pane {{ $finalActiveTab == 'przewoznicy' ? 'show active' : '' }}" id="przewoznicy">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie przewoźnikami</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> W tej sekcji możesz zarządzać przewoźnikami, ich danymi kontaktowymi i przypisanymi liniami.
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Dodaj nowego przewoźnika
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa</th>
                                    <th>Typ</th>
                                    <th>Liczba linii</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>PKP Intercity</td>
                                    <td>Kolej</td>
                                    <td>5</td>
                                    <td><span class="badge bg-success">Aktywny</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                            <button class="btn btn-sm btn-outline-danger">Dezaktywuj</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Ostatnie działania</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Użytkownik</th>
                            <th>Działanie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ now()->format('Y-m-d H:i') }}</td>
                            <td>{{ Auth::user()->name }}</td>
                            <td>Logowanie do panelu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Nowe kontenery zakładek -->
    <div class="tab-pane {{ $finalActiveTab == 'lines' ? 'show active' : '' }}" id="lines">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Liniami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać liniami (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'routes' ? 'show active' : '' }}" id="routes">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Trasami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać trasami (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'cities' ? 'show active' : '' }}" id="cities">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Miastami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać miastami (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'stops' ? 'show active' : '' }}" id="stops">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Przystankami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać przystankami (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'schedules' ? 'show active' : '' }}" id="schedules">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Rozkładami Jazdy</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać rozkładami jazdy (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'departures' ? 'show active' : '' }}" id="departures">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Odjazdami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać odjazdami (CRUD).</p>
            </div>
        </div>
    </div>

    <div class="tab-pane {{ $finalActiveTab == 'tickets' ? 'show active' : '' }}" id="tickets">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Zarządzanie Biletami</h5>
            </div>
            <div class="card-body">
                <p>Tutaj będzie można zarządzać biletami (CRUD).</p>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    /* Additional custom styles (if needed) */
    .card-header.bg-primary {
        background-color: #0077cc !important;
    }
    
    .btn-primary {
        background-color: #0077cc;
        border-color: #0077cc;
    }
    
    .btn-primary:hover {
        background-color: #005fa3;
        border-color: #005fa3;
    }
    
    .btn-outline-primary {
        color: #0077cc;
        border: 1px solid #0077cc; /* Ensure border is defined */
        background-color: transparent; /* Key for outline buttons */
    }
    
    .btn-outline-primary:hover {
        color: #fff; /* White text on hover */
        background-color: #0077cc;
        border-color: #0077cc;
    }

    .btn-outline-secondary {
        color: #6c757d; /* Bootstrap secondary text color */
        border: 1px solid #6c757d; /* Bootstrap secondary border color */
        background-color: transparent;
    }
    .btn-outline-secondary:hover {
        color: #fff; /* White text on hover */
        background-color: #6c757d; /* Bootstrap secondary color as background */
        border-color: #6c757d;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Panel Administratora');
        console.log('URL strony:', window.location.href);
        
        // Tab click handler
        const tabButtons = document.querySelectorAll('.tab-button');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                console.log('Kliknięto przycisk zakładki:', tabId);
                
                // Update URL without page refresh
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tabId);
                window.history.pushState({}, '', url);
                
                // Hide all tabs
                document.querySelectorAll('.tab-pane').forEach(tab => {
                    tab.classList.remove('show', 'active');
                });
                
                // Show selected tab
                const selectedPanel = document.getElementById(tabId);
                if (selectedPanel) {
                    selectedPanel.classList.add('show', 'active');
                }
                
                // Reset main tab button styles
                document.querySelectorAll('.main-tab-button').forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                
                // Aktywuj kliknięty główny przycisk zakładki lub odpowiadający mu, jeśli kliknięto przycisk w karcie
                const correspondingMainTabButton = document.querySelector(`.main-tab-button[data-tab="${tabId}"]`);
                if (correspondingMainTabButton) {
                    correspondingMainTabButton.classList.add('active', 'btn-primary');
                    correspondingMainTabButton.classList.remove('btn-outline-secondary');
                }
            });
        });
        
        // Add styles for tabs
        const style = document.createElement('style');
        style.textContent = `
            .tabs-container {
                margin-bottom: 20px;
            }
            .tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .tab-button {
                padding: 8px 16px;
                cursor: pointer;
            }
            .tab-pane {
                display: none;
            }
            .tab-pane.show.active {
                display: block;
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
