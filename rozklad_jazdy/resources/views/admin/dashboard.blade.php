@extends('layouts.app')

@section('title', 'Dashboard - Panel administratora')

@section('header', 'Dashboard')

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
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Użytkownicy</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $users }}</div>
                    <div class="mt-2">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Przewoźnicy</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $carriers }}</div>
                    <div class="mt-2">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Linie</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $lines }}</div>
                    <div class="mt-2">
                        <i class="fas fa-route fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 border-left-warning shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pojazdy</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $vehicles }}</div>
                    <div class="mt-2">
                        <i class="fas fa-bus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Trasy miejskie</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $cityRoutes }}</div>
                    <div class="mt-2">
                        <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Trasy międzymiastowe</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $intercityRoutes }}</div>
                    <div class="mt-2">
                        <i class="fas fa-road fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Przystanki</div>
                    <div class="h1 mb-0 font-weight-bold">{{ $stops }}</div>
                    <div class="mt-2">
                        <i class="fas fa-map-pin fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tabs-container mb-4">
        <div class="tabs">
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'aktywnosc' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="aktywnosc">Aktywność</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'popularnosc' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="popularnosc">Popularność tras</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'sprzedaz' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="sprzedaz">Sprzedaż</button>
            <button class="tab-button main-tab-button {{ $finalActiveTab == 'obciazenie' ? 'active btn btn-primary' : 'btn btn-outline-secondary' }}" data-tab="obciazenie">Obciążenie systemu</button>
        </div>
    </div>
    
    <div class="tab-content">
        <!-- Aktywność -->
        <div class="tab-pane {{ $finalActiveTab == 'aktywnosc' ? 'show active' : '' }}" id="aktywnosc">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Aktywność systemu</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Ilość logowań dzisiaj</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="h2">{{ $loginsToday ?? '24' }}</div>
                                        <div>
                                            <i class="fas fa-sign-in-alt fa-3x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-2">Wzrost o 12% w stosunku do wczoraj</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Ostatnie wyszukiwania</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="h2">{{ $searchesToday ?? '145' }}</div>
                                        <div>
                                            <i class="fas fa-search fa-3x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-2">Wzrost o 6% w stosunku do wczoraj</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Ostatnie logowania</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Użytkownik</th>
                                            <th>Czas</th>
                                            <th>Status</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>admin@example.com</td>
                                            <td>{{ now()->subHours(1)->format('Y-m-d H:i:s') }}</td>
                                            <td><span class="badge bg-success">Sukces</span></td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <tr>
                                            <td>moderator@example.com</td>
                                            <td>{{ now()->subHours(3)->format('Y-m-d H:i:s') }}</td>
                                            <td><span class="badge bg-success">Sukces</span></td>
                                            <td>192.168.1.105</td>
                                        </tr>
                                        <tr>
                                            <td>user@example.com</td>
                                            <td>{{ now()->subHours(5)->format('Y-m-d H:i:s') }}</td>
                                            <td><span class="badge bg-danger">Błąd</span></td>
                                            <td>192.168.1.110</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Popularność tras -->
        <div class="tab-pane {{ $finalActiveTab == 'popularnosc' ? 'show active' : '' }}" id="popularnosc">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Popularność tras</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Najbardziej popularne trasy</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Trasa</th>
                                                    <th>Typ</th>
                                                    <th>Odsłony</th>
                                                    <th>Wyszukiwania</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Warszawa - Kraków</td>
                                                    <td>Międzymiastowa</td>
                                                    <td>2453</td>
                                                    <td>876</td>
                                                </tr>
                                                <tr>
                                                    <td>Warszawa - Gdańsk</td>
                                                    <td>Międzymiastowa</td>
                                                    <td>2214</td>
                                                    <td>752</td>
                                                </tr>
                                                <tr>
                                                    <td>Linia M1 (Kabaty - Młociny)</td>
                                                    <td>Miejska</td>
                                                    <td>1998</td>
                                                    <td>650</td>
                                                </tr>
                                                <tr>
                                                    <td>Kraków - Zakopane</td>
                                                    <td>Międzymiastowa</td>
                                                    <td>1854</td>
                                                    <td>621</td>
                                                </tr>
                                                <tr>
                                                    <td>Linia 175 (Lotnisko - Centrum)</td>
                                                    <td>Miejska</td>
                                                    <td>1642</td>
                                                    <td>589</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Podział popularności</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <canvas id="routeTypeChart" width="100%" height="200"></canvas>
                                    </div>
                                    <div class="small text-muted mb-3">Podział wyszukiwań tras według typu</div>
                                    <hr>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Trasy międzymiastowe</span>
                                            <span class="text-primary">65%</span>
                                        </div>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Trasy miejskie</span>
                                            <span class="text-success">35%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sprzedaż -->
        <div class="tab-pane {{ $finalActiveTab == 'sprzedaz' ? 'show active' : '' }}" id="sprzedaz">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Statystyki sprzedaży</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Sprzedaż biletów w ostatnim miesiącu</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="salesChart" style="height: 300px"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow border-left-success">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Przychód (ostatni miesiąc)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="h1 mb-0 font-weight-bold text-gray-800">{{ number_format($revenue ?? 48250, 2, ',', ' ') }} PLN</div>
                                    <div class="text-success mt-2">
                                        <i class="fas fa-arrow-up"></i> Wzrost o 8.3% w porównaniu do poprzedniego miesiąca
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card shadow border-left-info mt-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info">Statystyki biletów</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bilety sprzedane</div>
                                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ticketsSold ?? 1842 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Średnio dziennie</div>
                                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ticketsDaily ?? 62 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sprzedaż według rodzaju biletu</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Rodzaj biletu</th>
                                            <th>Ilość</th>
                                            <th>Wartość</th>
                                            <th>Średnia dziennie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Jednorazowy</td>
                                            <td>854</td>
                                            <td>12 810,00 PLN</td>
                                            <td>28</td>
                                        </tr>
                                        <tr>
                                            <td>Miesięczny</td>
                                            <td>412</td>
                                            <td>28 840,00 PLN</td>
                                            <td>14</td>
                                        </tr>
                                        <tr>
                                            <td>Ulgowy</td>
                                            <td>576</td>
                                            <td>6 600,00 PLN</td>
                                            <td>19</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Obciążenie systemu -->
        <div class="tab-pane {{ $finalActiveTab == 'obciazenie' ? 'show active' : '' }}" id="obciazenie">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-server me-2"></i>Obciążenie systemu</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Wykorzystanie CPU</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $cpuUsage ?? '32%' }}</div>
                                    <div class="mt-2">
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 32%" aria-valuenow="32" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow border-left-success">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pamięć RAM</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $memoryUsage ?? '47%' }}</div>
                                    <div class="mt-2">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow border-left-warning">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Dysk</div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $diskUsage ?? '65%' }}</div>
                                    <div class="mt-2">
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Obciążenie serwera w czasie</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="serverLoadChart" style="height: 300px"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Status usług</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Usługa</th>
                                                    <th>Status</th>
                                                    <th>Czas działania</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Web Serwer</td>
                                                    <td><span class="badge bg-success">Aktywny</span></td>
                                                    <td>15 dni 6 godz.</td>
                                                </tr>
                                                <tr>
                                                    <td>Baza danych</td>
                                                    <td><span class="badge bg-success">Aktywna</span></td>
                                                    <td>15 dni 6 godz.</td>
                                                </tr>
                                                <tr>
                                                    <td>Queue Worker</td>
                                                    <td><span class="badge bg-success">Aktywny</span></td>
                                                    <td>8 dni 23 godz.</td>
                                                </tr>
                                                <tr>
                                                    <td>Scheduler</td>
                                                    <td><span class="badge bg-success">Aktywny</span></td>
                                                    <td>15 dni 6 godz.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Ostatnie błędy systemu</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Czas</th>
                                                    <th>Kod</th>
                                                    <th>Wiadomość</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ now()->subHours(3)->format('Y-m-d H:i') }}</td>
                                                    <td>404</td>
                                                    <td>Route not found: /api/routes/invalid</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ now()->subHours(8)->format('Y-m-d H:i') }}</td>
                                                    <td>500</td>
                                                    <td>Database connection error (resolved)</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ now()->subDays(1)->format('Y-m-d H:i') }}</td>
                                                    <td>403</td>
                                                    <td>Forbidden access attempt: admin/settings</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        
        <!-- Miasta -->
        <div class="tab-pane {{ $finalActiveTab == 'cities' ? 'show active' : '' }}" id="cities">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Miastami</h5>
                </div>
                <div class="card-body">
                    <p>Przejdź do pełnej listy miast, aby zarządzać nimi (dodawać, edytować, usuwać).</p>
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">Przejdź do Listy Miast</a>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Dashboard Administracyjny');
        
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
                
                // Aktywuj kliknięty główny przycisk zakładki
                const correspondingMainTabButton = document.querySelector(`.main-tab-button[data-tab="${tabId}"]`);
                if (correspondingMainTabButton) {
                    correspondingMainTabButton.classList.add('active', 'btn-primary');
                    correspondingMainTabButton.classList.remove('btn-outline-secondary');
                }
            });
        });
        
        // Inicjalizacja wykresów tylko jeśli istnieją kontenery
        // Wykres popularności tras według typu
        if (document.getElementById('routeTypeChart')) {
            const routeTypeCtx = document.getElementById('routeTypeChart').getContext('2d');
            new Chart(routeTypeCtx, {
                type: 'pie',
                data: {
                    labels: ['Międzymiastowe', 'Miejskie'],
                    datasets: [{
                        data: [65, 35],
                        backgroundColor: ['#4e73df', '#1cc88a'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Wykres sprzedaży biletów
        if (document.getElementById('salesChart')) {
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [
                        "1 cze", "2 cze", "3 cze", "4 cze", "5 cze", "6 cze", "7 cze", 
                        "8 cze", "9 cze", "10 cze", "11 cze", "12 cze", "13 cze", "14 cze",
                        "15 cze", "16 cze", "17 cze", "18 cze", "19 cze", "20 cze", "21 cze",
                        "22 cze", "23 cze", "24 cze", "25 cze", "26 cze", "27 cze", "28 cze",
                        "29 cze", "30 cze"
                    ],
                    datasets: [{
                        label: "Liczba sprzedanych biletów",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: [52, 48, 60, 70, 58, 45, 65, 55, 75, 80, 62, 68, 73, 78, 80, 85, 70, 65, 60, 55, 50, 58, 62, 68, 72, 80, 75, 70, 68, 60]
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10
                            }
                        },
                        x: {
                            ticks: {
                                maxTicksLimit: 7,
                                padding: 10
                            }
                        }
                    }
                }
            });
        }
        
        // Wykres obciążenia serwera
        if (document.getElementById('serverLoadChart')) {
            const serverLoadCtx = document.getElementById('serverLoadChart').getContext('2d');
            new Chart(serverLoadCtx, {
                type: 'line',
                data: {
                    labels: ["00:00", "02:00", "04:00", "06:00", "08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00", "22:00"],
                    datasets: [{
                        label: "CPU",
                        lineTension: 0.3,
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        fill: false,
                        data: [20, 18, 15, 14, 30, 45, 40, 35, 50, 45, 35, 25]
                    },
                    {
                        label: "Pamięć RAM",
                        lineTension: 0.3,
                        borderColor: "rgba(28, 200, 138, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(28, 200, 138, 1)",
                        pointBorderColor: "rgba(28, 200, 138, 1)",
                        pointHoverRadius: 3,
                        fill: false,
                        data: [35, 32, 30, 30, 35, 40, 45, 50, 55, 60, 50, 45]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            ticks: {
                                padding: 10
                            }
                        }
                    }
                }
            });
        }
        
        // Add styles for tabs and cards with borders
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
            .border-left-primary {
                border-left: 4px solid #4e73df;
            }
            .border-left-success {
                border-left: 4px solid #1cc88a;
            }
            .border-left-info {
                border-left: 4px solid #36b9cc;
            }
            .border-left-warning {
                border-left: 4px solid #f6c23e;
            }
            .text-gray-300 {
                color: #dddfeb;
            }
            .text-gray-800 {
                color: #5a5c69;
            }
            .text-primary {
                color: #4e73df !important;
            }
            .text-success {
                color: #1cc88a !important;
            }
            .text-info {
                color: #36b9cc !important;
            }
            .text-warning {
                color: #f6c23e !important;
            }
            .font-weight-bold {
                font-weight: bold !important;
            }
            .card-header {
                background-color: #f8f9fc;
                border-bottom: 1px solid #e3e6f0;
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
