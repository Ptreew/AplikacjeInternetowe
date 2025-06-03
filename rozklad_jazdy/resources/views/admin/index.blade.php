@extends('layouts.app')

@section('title', 'Panel Administratora')

@section('header', 'Panel Administratora')

{{-- Navigation is handled in the main layout --}}

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Przejdź do Dashboard</a>
            </div>
        </div>
        
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="miedzymiastowe-tab" data-bs-toggle="tab" data-bs-target="#miedzymiastowe" type="button" role="tab" aria-controls="miedzymiastowe" aria-selected="true">Międzymiastowe</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="miejskie-tab" data-bs-toggle="tab" data-bs-target="#miejskie" type="button" role="tab" aria-controls="miejskie" aria-selected="false">Miejskie</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="uzytkownicy-tab" data-bs-toggle="tab" data-bs-target="#uzytkownicy" type="button" role="tab" aria-controls="uzytkownicy" aria-selected="false">Użytkownicy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pojazdy-tab" data-bs-toggle="tab" data-bs-target="#pojazdy" type="button" role="tab" aria-controls="pojazdy" aria-selected="false">Pojazdy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="przewoznicy-tab" data-bs-toggle="tab" data-bs-target="#przewoznicy" type="button" role="tab" aria-controls="przewoznicy" aria-selected="false">Przewoźnicy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lines-tab" data-bs-toggle="tab" data-bs-target="#lines" type="button" role="tab" aria-controls="lines" aria-selected="false">Linie</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="routes-tab" data-bs-toggle="tab" data-bs-target="#routes" type="button" role="tab" aria-controls="routes" aria-selected="false">Trasy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cities-tab" data-bs-toggle="tab" data-bs-target="#cities" type="button" role="tab" aria-controls="cities" aria-selected="false">Miasta</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stops-tab" data-bs-toggle="tab" data-bs-target="#stops" type="button" role="tab" aria-controls="stops" aria-selected="false">Przystanki</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedules" type="button" role="tab" aria-controls="schedules" aria-selected="false">Rozkłady</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="departures-tab" data-bs-toggle="tab" data-bs-target="#departures" type="button" role="tab" aria-controls="departures" aria-selected="false">Odjazdy</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab" aria-controls="tickets" aria-selected="false">Bilety</button>
            </li>
        </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Międzymiastowe -->
        <div class="tab-pane fade show active" id="miedzymiastowe" role="tabpanel" aria-labelledby="miedzymiastowe-tab">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Najnowsze kursy międzymiastowe</h5>
                    <a href="{{ route('admin.intercity.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Dodaj nowy kurs
                    </a>
                </div>
                <div class="card-body">
                    @php
                        // Pobierz kursy międzymiastowe z bazy danych
                        $intercityRoutes = \App\Models\Route::with(['line.carrier', 'routeStops.stop.city', 'schedules'])
                            ->whereHas('line', function($query) {
                                $query->whereNull('number'); // Kursy międzymiastowe mają NULL w polu number
                            })
                            ->orderBy('id', 'desc')
                            ->get();
                    @endphp
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Linia</th>
                                    <th>Przewoźnik</th>
                                    <th>Miasto początkowe</th>
                                    <th>Miasto docelowe</th>
                                    <th>Dni kursowania</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($intercityRoutes as $route)
                                    <tr>
                                        <td>{{ $route->id }}</td>
                                        <td>{{ $route->line->number }} - {{ $route->line->name }}</td>
                                        <td>{{ $route->line->carrier->name }}</td>
                                        <td>
                                            @if($route->routeStops->isNotEmpty() && $route->routeStops->first()->stop && $route->routeStops->first()->stop->city)
                                                {{ $route->routeStops->first()->stop->city->name }}
                                                ({{ $route->routeStops->first()->stop->name }})
                                            @else
                                                Brak danych
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->routeStops->count() > 1 && $route->routeStops->last()->stop && $route->routeStops->last()->stop->city)
                                                {{ $route->routeStops->last()->stop->city->name }}
                                                ({{ $route->routeStops->last()->stop->name }})
                                            @else
                                                Brak danych
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->schedules->isNotEmpty())
                                                @php
                                                    $dayNames = [
                                                        0 => 'Nd',
                                                        1 => 'Pn',
                                                        2 => 'Wt',
                                                        3 => 'Śr',
                                                        4 => 'Cz',
                                                        5 => 'Pt',
                                                        6 => 'Sb'
                                                    ];
                                                    
                                                    $allDays = [];
                                                    if ($route->schedules) { // Ensure schedules collection exists
                                                        foreach($route->schedules as $schedule) {
                                                            if (is_array($schedule->days_of_week)) { // Check if days_of_week is an array
                                                                foreach($schedule->days_of_week as $day) {
                                                                    // Ensure $day is a valid key for $dayNames and not already added
                                                                    if (isset($dayNames[$day]) && !isset($allDays[$day])) { 
                                                                        $allDays[$day] = $dayNames[$day];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ksort($allDays); // ksort works fine on an empty array
                                                @endphp
                                                {{ implode(', ', $allDays) }}
                                            @else
                                                <span class="text-muted">Brak danych</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->is_active)
                                                <span class="badge bg-success">Aktywny</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.intercity.edit', $route->id) }}" class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <form action="{{ route('admin.intercity.destroy', $route->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten kurs?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Brak kursów międzymiastowych w bazie danych</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.intercity.index') }}" class="btn btn-primary">
                            Zobacz wszystkie kursy międzymiastowe
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Miejskie -->
        <div class="tab-pane fade" id="miejskie" role="tabpanel" aria-labelledby="miejskie-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie autobusami miejskimi</h5>
                </div>
                <div class="card-body">
                    <form id="form-miejskie" method="POST" action="{{ route('admin.miejskie.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="miasto" name="miasto" placeholder="Miasto" required />
                                <label for="miasto">Miasto</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="numer_linii" name="numer_linii" placeholder="Numer linii" required />
                                <label for="numer_linii">Numer linii</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="godzina_startowa" name="godzina_startowa" required />
                                <label for="godzina_startowa">Godzina startowa</label>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Przystanki</h5>
                                </div>
                                <div class="card-body" id="przystanki-wrapper">
                                    <!-- Dodawanie przystanków -->
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-outline-primary" id="dodaj-przystanek">
                                        <i class="bi bi-plus-circle"></i> Dodaj przystanek
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Dodaj kurs</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista linii autobusowych</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Linia</th>
                                    <th>Start</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Linia 12 – Kraków</td>
                                    <td>07:00</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info toggle-stops" data-bs-toggle="collapse" data-bs-target="#przystanki-1">
                                            Pokaż przystanki
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                        <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                    </td>
                                </tr>
                                <tr class="collapse" id="przystanki-1">
                                    <td colspan="3">
                                        <div class="card card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item">Dworzec Główny – 07:00</li>
                                                <li class="list-group-item">Rondo Grunwaldzkie – 07:12</li>
                                                <li class="list-group-item">Nowa Huta – 07:30</li>
                                            </ul>
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
        <div class="tab-pane fade" id="uzytkownicy" role="tabpanel" aria-labelledby="uzytkownicy-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie użytkownikami</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Email</th>
                                    <th>Nazwa użytkownika</th>
                                    <th>Rola</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $users = \App\Models\User::orderBy('name')->paginate(10);
                                @endphp
                                
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            @if($user->role == 'admin')
                                                <span class="badge bg-success">Administrator</span>
                                            @else
                                                <span class="badge bg-primary">Użytkownik</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if($user->id != auth()->id())
                                                    <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#changeRoleModal{{ $user->id }}">
                                                        Zmień rolę
                                                    </button>
                                                    
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">
                                                            Usuń
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-secondary">Aktualnie zalogowany</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Modal do zmiany roli -->
                                            <div class="modal fade" id="changeRoleModal{{ $user->id }}" tabindex="-1" aria-labelledby="changeRoleModalLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="changeRoleModalLabel{{ $user->id }}">Zmień rolę użytkownika</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="role" class="form-label">Rola</label>
                                                                    <select class="form-select" id="role" name="role" required>
                                                                        <option value="standard" {{ $user->role == 'standard' ? 'selected' : '' }}>Użytkownik</option>
                                                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Brak użytkowników w bazie danych</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-center">
                        {{ $users->links() }}
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            Zobacz wszystkich użytkowników
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pojazdy -->
        <div class="tab-pane fade" id="pojazdy" role="tabpanel" aria-labelledby="pojazdy-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie pojazdami</h5>
                    <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Dodaj nowy pojazd
                    </a>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Numer pojazdu</th>
                                    <th>Typ</th>
                                    <th>Linia</th>
                                    <th>Przewoźnik</th>
                                    <th>Pojemność</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $vehicles = \App\Models\Vehicle::with(['line', 'line.carrier'])->take(10)->get();
                                @endphp
                                
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->id }}</td>
                                        <td>{{ $vehicle->vehicle_number }}</td>
                                        <td>{{ $vehicle->type }}</td>
                                        <td>{{ $vehicle->line->name ?? 'Brak linii' }}</td>
                                        <td>{{ $vehicle->line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                        <td>{{ $vehicle->capacity }}</td>
                                        <td>
                                            @if($vehicle->is_active)
                                                <span class="badge bg-success">Aktywny</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-sm btn-success me-1">
                                                    Pokaż
                                                </a>
                                                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten pojazd?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Brak pojazdów w bazie danych</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    

                    
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Zobacz pełną listę pojazdów
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Przewoźnicy -->
        <div class="tab-pane fade" id="przewoznicy" role="tabpanel" aria-labelledby="przewoznicy-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie Przewoźnikami</h5>
                    <a href="{{ route('admin.carriers.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Dodaj Przewoźnika
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa</th>
                                    <th>Email</th>
                                    <th>Strona internetowa</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $carriers = \App\Models\Carrier::take(10)->get();
                                @endphp
                                
                                @foreach($carriers as $carrier)
                                    <tr>
                                        <td>{{ $carrier->id }}</td>
                                        <td>{{ $carrier->name }}</td>
                                        <td>{{ $carrier->email }}</td>
                                        <td>
                                            <a href="{{ $carrier->website }}" target="_blank">{{ $carrier->website }}</a>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.carriers.edit', $carrier) }}" 
                                                   class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <a href="{{ route('admin.carriers.show', $carrier) }}" 
                                                   class="btn btn-sm btn-success me-1">
                                                    Pokaż
                                                </a>
                                                <form action="{{ route('admin.carriers.destroy', $carrier) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Czy na pewno chcesz usunąć tego przewoźnika?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('admin.carriers.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Zobacz pełną listę przewoźników
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nowe kontenery zakładek -->
        <div class="tab-pane fade" id="lines" role="tabpanel" aria-labelledby="lines-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie Liniami</h5>
                    <a href="{{ route('admin.lines.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Dodaj nową linię
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Numer</th>
                                    <th>Nazwa</th>
                                    <th>Przewoźnik</th>
                                    <th>Kolor</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $lines = \App\Models\Line::with('carrier')->take(10)->get();
                                @endphp
                                
                                @forelse($lines as $line)
                                    <tr>
                                        <td>{{ $line->id }}</td>
                                        <td>
                                            @if($line->number)
                                                {{ $line->number }}
                                            @else
                                                <span class="fst-italic text-muted">Kurs międzymiastowy</span>
                                            @endif
                                        </td>
                                        <td>{{ $line->name }}</td>
                                        <td>{{ $line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                        <td>
                                            <span class="color-box" style="background-color: {{ $line->color }}; display: inline-block; width: 20px; height: 20px; margin-right: 5px; border: 1px solid #ccc;"></span>
                                            {{ $line->color }}
                                        </td>
                                        <td>
                                            @if($line->is_active)
                                                <span class="badge bg-success">Aktywna</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywna</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.lines.edit', $line) }}" class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <a href="{{ route('admin.lines.show', $line) }}" class="btn btn-sm btn-success me-1">
                                                    Pokaż
                                                </a>
                                                <form action="{{ route('admin.lines.destroy', $line) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę linię?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Brak linii w bazie danych</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    

                    
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ route('admin.lines.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Zobacz pełną listę linii
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="routes" role="tabpanel" aria-labelledby="routes-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie Trasami</h5>
                    <a href="{{ route('admin.routes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Dodaj nową trasę
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa</th>
                                    <th>Linia</th>
                                    <th>Przewoźnik</th>
                                    <th>Czas podróży</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $routes = \App\Models\Route::with(['line', 'line.carrier'])->take(10)->get();
                                @endphp
                                
                                @forelse($routes as $route)
                                    <tr>
                                        <td>{{ $route->id }}</td>
                                        <td>{{ $route->name }}</td>
                                        <td>
                                            @if($route->line->number)
                                                {{ $route->line->number }}
                                            @else
                                                <span class="fst-italic text-muted">Kurs międzymiastowy</span>
                                            @endif
                                        </td>
                                        <td>{{ $route->line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                        <td>
                                            @if($route->travel_time)
                                                {{ $route->travel_time }} min
                                            @else
                                                <span class="text-muted">Nie określono</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->is_active)
                                                <span class="badge bg-success">Aktywna</span>
                                            @else
                                                <span class="badge bg-danger">Nieaktywna</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <a href="{{ route('admin.routes.show', $route) }}" class="btn btn-sm btn-success me-1">
                                                    Pokaż
                                                </a>
                                                <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę trasę?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Brak tras w bazie danych</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ route('admin.routes.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Zobacz pełną listę tras
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="cities" role="tabpanel" aria-labelledby="cities-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie Miastami</h5>
                    <a href="{{ route('admin.cities.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Dodaj Miasto
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa</th>
                                    <th>Województwo</th>
                                    <th>Liczba przystanków</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\City::with('stops')->take(10)->get() as $city)
                                    <tr>
                                        <td>{{ $city->id }}</td>
                                        <td>{{ $city->name }}</td>
                                        <td>{{ $city->voivodeship }}</td>
                                        <td>{{ $city->stops->count() }}</td>
                                        <td class="align-middle">
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.cities.edit', $city) }}" 
                                                   class="btn btn-sm btn-primary me-1">
                                                    Edytuj
                                                </a>
                                                <a href="{{ route('admin.cities.show', $city) }}" 
                                                   class="btn btn-sm btn-success me-1">
                                                    Pokaż
                                                </a>
                                                <form action="{{ route('admin.cities.destroy', $city) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Czy na pewno chcesz usunąć to miasto?')">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Zobacz pełną listę miast
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="stops" role="tabpanel" aria-labelledby="stops-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Zarządzanie Przystankami</h5>
                    <a href="{{ route('admin.stops.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Dodaj Przystanek
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa Przystanku</th>
                                    <th>Kod</th>
                                    <th>Miasto</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stops as $stop)
                                    <tr>
                                        <td>{{ $stop->id }}</td>
                                        <td>{{ $stop->name }}</td>
                                        <td>{{ $stop->code }}</td>
                                        <td>{{ $stop->city->name }}</td>
                                        <td>{{ $stop->is_active ? 'Aktywny' : 'Nieaktywny' }}</td>
                                        <td>
                                            <a href="{{ route('admin.stops.edit', $stop) }}" class="btn btn-sm btn-primary me-1">
                                                Edytuj
                                            </a>
                                            <form action="{{ route('admin.stops.destroy', $stop) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten przystanek?')">
                                                    Usuń
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ route('admin.stops.index') }}" class="btn btn-primary">
                            Zobacz pełną listę przystanków
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="schedules" role="tabpanel" aria-labelledby="schedules-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Rozkładami Jazdy</h5>
                </div>
                <div class="card-body">
                    <p>Tutaj będzie można zarządzać rozkładami jazdy (CRUD).</p>
                    <!-- TODO: Add CRUD interface for Schedules -->
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="departures" role="tabpanel" aria-labelledby="departures-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Odjazdami</h5>
                </div>
                <div class="card-body">
                    <p>Tutaj będzie można zarządzać odjazdami (CRUD).</p>
                    <!-- TODO: Add CRUD interface for Departures -->
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Biletami</h5>
                </div>
                <div class="card-body">
                    @php
                        // Pobierz najnowsze bilety z bazy danych
                        $latestTickets = \App\Models\Ticket::with(['user', 'departure.schedule.route.line'])
                            ->orderBy('purchase_date', 'desc')
                            ->take(10)
                            ->get();
                    @endphp
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nr biletu</th>
                                    <th>Pasażer</th>
                                    <th>Trasa</th>
                                    <th>Data odjazdu</th>
                                    <th>Status</th>
                                    <th>Data zakupu</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_number }}</td>
                                        <td>{{ $ticket->passenger_name }}</td>
                                        <td>
                                            @if($ticket->departure && $ticket->departure->schedule && $ticket->departure->schedule->route)
                                                {{ $ticket->departure->schedule->route->name }}
                                            @else
                                                <span class="text-muted">Brak danych</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->departure)
                                                @if($ticket->departure->departure_time instanceof \Carbon\Carbon)
                                                    {{ $ticket->departure->departure_time->format('d.m.Y H:i') }}
                                                @else
                                                    {{ $ticket->departure->departure_time }}
                                                @endif
                                            @else
                                                <span class="text-muted">Brak danych</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'reserved' => 'warning',
                                                    'paid' => 'success',
                                                    'used' => 'success',
                                                    'cancelled' => 'danger'
                                                ][$ticket->status] ?? 'secondary';
                                                
                                                $statusLabel = [
                                                    'reserved' => 'Zarezerwowany',
                                                    'paid' => 'Opłacony',
                                                    'used' => 'Wykorzystany',
                                                    'cancelled' => 'Anulowany'
                                                ][$ticket->status] ?? 'Nieznany';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>
                                            @if($ticket->purchase_date instanceof \Carbon\Carbon)
                                                {{ $ticket->purchase_date->format('d.m.Y') }}
                                            @elseif($ticket->purchase_date)
                                                {{ $ticket->purchase_date }}
                                            @else
                                                <span class="text-muted">Brak danych</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex">
                                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-success me-1" title="Szczegóły">
                                                    Szczegóły
                                                </a>
                                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary me-1" title="Edytuj">
                                                    Edytuj
                                                </a>
                                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć ten bilet?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Usuń">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Brak biletów w systemie</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary">
                            Przejdź do pełnego zarządzania biletami
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('styles')
<style>
    /* Custom styles for admin panel tabs */
    .nav-tabs .nav-link:not(.active) {
        color: #6c757d; /* Bootstrap secondary color (gray) */
        background-color: transparent;
        border-bottom-color: #dee2e6; /* Match default bottom border */
    }

    .nav-tabs .nav-link:not(.active):hover {
        color: #495057; /* Darker gray on hover */
        border-bottom-color: #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #fff; /* White text for active tab */
        background-color: #007bff; /* Bootstrap primary color */
        border-color: #007bff #007bff #fff;
    }
</style>
@endsection

@section('scripts')
    <script>
        
        // Function to get URL parameters
        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
        
        // Function to update URL with tab parameter without page refresh
        function updateURLWithTab(tabId) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        }
        
        // Check for tab parameter on page load and activate the correct tab
        document.addEventListener('DOMContentLoaded', function() {
            const tabParam = getURLParameter('tab');
            if (tabParam) {
                // Find the tab to activate
                const tabToActivate = document.querySelector(`#${tabParam}-tab`);
                if (tabToActivate) {
                    // Using Bootstrap's tab API to show the tab
                    const tab = new bootstrap.Tab(tabToActivate);
                    tab.show();
                }
            }
            
            // Add event listeners to tabs to update URL when clicked
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    const targetTabId = event.target.getAttribute('aria-controls');
                    updateURLWithTab(targetTabId);
                });
            });
        });
        
        // Add event listeners to tabs to update URL when clicked
        const dodajBtn = document.getElementById("dodaj-przystanek");
        const wrapper = document.getElementById("przystanki-wrapper");
        
        if (dodajBtn && wrapper) {
            dodajBtn.addEventListener("click", () => {
                const container = document.createElement("div");
                container.classList.add("row", "mb-2", "przystanek-blok");
                container.innerHTML = `
                    <div class="col-md-5">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="przystanki[]" id="przystanek-${Date.now()}" placeholder="Nazwa przystanku" required />
                            <label for="przystanek-${Date.now()}">Nazwa przystanku</label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-floating">
                            <input type="time" class="form-control" name="godziny[]" id="godzina-${Date.now()}" required />
                            <label for="godzina-${Date.now()}">Godzina</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-outline-danger usun-przystanek">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                container.querySelector(".usun-przystanek").addEventListener("click", () => {
                    container.remove();
                });
                wrapper.appendChild(container);
            });
        }
    </script>
@endsection
