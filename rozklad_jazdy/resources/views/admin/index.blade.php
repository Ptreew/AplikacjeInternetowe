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
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie kursami międzymiastowymi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.miedzymiastowe.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="z" name="z" placeholder="Z" required />
                                <label for="z">Z</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="do" name="do" placeholder="Do" required />
                                <label for="do">Do</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="przystanek_poczatkowy" name="przystanek_poczatkowy" placeholder="Przystanek początkowy" required />
                                <label for="przystanek_poczatkowy">Przystanek początkowy</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="przystanek_koncowy" name="przystanek_koncowy" placeholder="Przystanek końcowy" required />
                                <label for="przystanek_koncowy">Przystanek końcowy</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="godzina_wyruszenia" name="godzina_wyruszenia" required />
                                <label for="godzina_wyruszenia">Godzina wyruszenia</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="godzina_dotarcia" name="godzina_dotarcia" required />
                                <label for="godzina_dotarcia">Godzina dotarcia</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="przewoznik" name="przewoznik" placeholder="Przewoźnik" required />
                                <label for="przewoznik">Przewoźnik</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="cena" name="cena" placeholder="Cena biletu" required />
                                <label for="cena">Cena biletu</label>
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
                    <h5 class="mb-0">Lista kursów</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Z: Rzeszów</th>
                                    <th>&#8680;</th>
                                    <th>Do: Kraków</th>
                                    <th>19<sup><u>99</u></sup>zł</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>07:00</td>
                                    <td></td>
                                    <td>09:50</td>
                                    <td></td>
                                    <td><button class="btn btn-sm btn-outline-primary">Edytuj</button></td>
                                </tr>
                                <tr>
                                    <td>Dworzec Lokalny</td>
                                    <td>&rarr;</td>
                                    <td>Kraków MDA</td>
                                    <td>FlixBus</td>
                                    <td><button class="btn btn-sm btn-outline-danger">Usuń</button></td>
                                </tr>
                            </tbody>
                        </table>
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
                    <form method="POST" action="{{ route('admin.users.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Imię i nazwisko" required />
                                <label for="name">Imię i nazwisko</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required />
                                <label for="email">E-mail</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="role" name="role">
                                    <option value="user">Użytkownik</option>
                                    <option value="admin">Administrator</option>
                                </select>
                                <label for="role">Rola</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Dodaj użytkownika</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista użytkowników</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Imię</th>
                                    <th>Email</th>
                                    <th>Hasło</th>
                                    <th>Rola</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jan Kowalski</td>
                                    <td>jan@domena.pl</td>
                                    <td>85a47aa9e6a6e83b2ba86abc8871c290899b1f54</td>
                                    <td>Administrator</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                        <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pojazdy -->
        <div class="tab-pane fade" id="pojazdy" role="tabpanel" aria-labelledby="pojazdy-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie pojazdami</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.vehicles.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="registration" name="registration" placeholder="Numer rejestracyjny" required />
                                <label for="registration">Numer rejestracyjny</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="model" name="model" placeholder="Model" required />
                                <label for="model">Model</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="seats" name="seats" placeholder="Liczba miejsc" required />
                                <label for="seats">Liczba miejsc</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Dodaj pojazd</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista pojazdów</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Rejestracja</th>
                                    <th>Model</th>
                                    <th>Miejsca</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>RZ12345</td>
                                    <td>Mercedes Sprinter</td>
                                    <td>20</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                        <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Przewoźnicy -->
        <div class="tab-pane fade" id="przewoznicy" role="tabpanel" aria-labelledby="przewoznicy-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie przewoźnikami</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.carriers.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nazwa przewoźnika" required />
                                <label for="name">Nazwa przewoźnika</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telefon kontaktowy" />
                                <label for="phone">Telefon kontaktowy</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="url" class="form-control" id="website" name="website" placeholder="Strona internetowa" />
                                <label for="website">Strona internetowa</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Dodaj przewoźnika</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista przewoźników</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nazwa</th>
                                    <th>Telefon</th>
                                    <th>WWW</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FlixBus</td>
                                    <td>+48 123 456 789</td>
                                    <td><a href="#">flixbus.pl</a></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edytuj</button>
                                        <button class="btn btn-sm btn-outline-danger">Usuń</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nowe kontenery zakładek -->
        <div class="tab-pane fade" id="lines" role="tabpanel" aria-labelledby="lines-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Liniami</h5>
                </div>
                <div class="card-body">
                    <p>Tutaj będzie można zarządzać liniami (CRUD).</p>
                    <!-- TODO: Add CRUD interface for Lines -->
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="routes" role="tabpanel" aria-labelledby="routes-tab">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Zarządzanie Trasami</h5>
                </div>
                <div class="card-body">
                    <p>Tutaj będzie można zarządzać trasami (CRUD).</p>
                    <!-- TODO: Add CRUD interface for Routes -->
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
                    <p>Tutaj będzie można zarządzać biletami (CRUD).</p>
                    <!-- TODO: Add CRUD interface for Tickets -->
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
