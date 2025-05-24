@extends('layouts.app')

@section('title', 'Panel Administratora')

@section('header', 'Panel Administratora')

@section('navigation')
    <a href="{{ route('home') }}">Strona główna</a>
@endsection

@section('content')
    <div class="tabs">
        <button class="tab-button active" data-tab="miedzymiastowe">Międzymiastowe</button>
        <button class="tab-button" data-tab="miejskie">Miejskie</button>
        <button class="tab-button" data-tab="uzytkownicy">Użytkownicy</button>
        <button class="tab-button" data-tab="pojazdy">Pojazdy</button>
        <button class="tab-button" data-tab="przewoznicy">Przewoźnicy</button>
    </div>

    <!-- Międzymiastowe -->
    <section class="tab-content" id="miedzymiastowe">
        <h2>Zarządzanie kursami międzymiastowymi</h2>
        <form method="POST" action="{{ route('admin.miedzymiastowe.store') }}">
            @csrf
            <input type="text" name="z" placeholder="Z" required />
            <input type="text" name="do" placeholder="Do" required /><br/>
            <input type="text" name="przystanek_poczatkowy" placeholder="Przystanek początkowy" required />
            <input type="text" name="przystanek_koncowy" placeholder="Przystanek końcowy" required /><br/>
            <input type="time" name="godzina_wyruszenia" placeholder="Godzina wyruszenia" required />
            <input type="time" name="godzina_dotarcia" placeholder="Godzina dotarcia" required /><br/>
            <input type="text" name="przewoznik" placeholder="Przewoźnik" required />
            <input type="text" name="cena" placeholder="Cena biletu" required /><br/>
            <button type="submit">Dodaj kurs</button>
        </form>
        <div class="admin-list">  
            <table width="100%">
                <tr>
                    <th>Z: Rzeszów</th>
                    <th>&#8680;</th>
                    <th>Do: Kraków</th>
                    <th>19<sup><u>99</u></sup>zł</th>
                    <th></th>
                </tr>
                <tr>
                    <td>07:00</td>
                    <td></td>
                    <td>09:50</td>
                    <td></td>
                    <td><button>Edytuj</button></td>
                </tr>
                <tr>
                    <td>Dworzec Lokalny</td>
                    <td>&rarr;</td>
                    <td>Kraków MDA</td>
                    <td>FlixBus</td>
                    <td><button>Usuń</button></td>
                </tr>
            </table>
        </div>
    </section>

    <!-- Miejskie -->
    <section class="tab-content hidden" id="miejskie">
        <h2>Zarządzanie autobusami miejskimi</h2>
        <form id="form-miejskie" method="POST" action="{{ route('admin.miejskie.store') }}">
            @csrf
            <input type="text" name="miasto" placeholder="Miasto" required />
            <input type="text" name="numer_linii" placeholder="Numer linii" required />
            <input type="time" name="godzina_startowa" placeholder="Godzina startowa" required /><br/>
            <div id="przystanki-wrapper">
                <h3>Przystanki:</h3>
                <!-- Dodawanie przystanków -->
            </div>
            <button type="button" id="dodaj-przystanek">Dodaj przystanek</button><br/><br/>
            <button type="submit">Dodaj kurs</button>
        </form>

        <div class="admin-list">
            <table width="100%">
                <tr>
                    <th>Linia 12 – Kraków</th>
                    <th>Start: 07:00</th>
                    <th></th>
                </tr>
                <tr>
                    <td colspan="2"><button class="toggle-stops">Pokaż przystanki</button></td>
                    <td><button>Edytuj</button> <button>Usuń</button></td>
                </tr>
                <tr class="przystanki hidden">
                    <td colspan="3">
                        <ul>
                            <li>Dworzec Główny – 07:00</li>
                            <li>Rondo Grunwaldzkie – 07:12</li>
                            <li>Nowa Huta – 07:30</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    </section>

    <!-- Użytkownicy -->
    <section class="tab-content hidden" id="uzytkownicy">
        <h2>Zarządzanie użytkownikami</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="text" name="name" placeholder="Imię i nazwisko" required />
            <input type="email" name="email" placeholder="E-mail" required />
            <select name="role">
                <option>Użytkownik</option>
                <option>Administrator</option>
            </select>
            <button type="submit">Dodaj użytkownika</button>
        </form>

        <div class="admin-list">
            <table>
                <tr><th>Imię</th><th>Email</th><th>Hasło</th><th>Rola</th><th>Akcja</th></tr>
                <tr><td>Jan Kowalski</td><td>jan@domena.pl</td><td>85a47aa9e6a6e83b2ba86abc8871c290899b1f54</td><td>Administrator</td><td><button>Edytuj</button></td><td><button>Usuń</button></td></tr>
            </table>
        </div>
    </section>

    <!-- Pojazdy -->
    <section class="tab-content hidden" id="pojazdy">
        <h2>Zarządzanie pojazdami</h2>
        <form method="POST" action="{{ route('admin.vehicles.store') }}">
            @csrf
            <input type="text" name="registration" placeholder="Numer rejestracyjny" required />
            <input type="text" name="model" placeholder="Model" required />
            <input type="number" name="seats" placeholder="Liczba miejsc" required />
            <button type="submit">Dodaj pojazd</button>
        </form>

        <div class="admin-list">
            <table>
                <tr><th>Rejestracja</th><th>Model</th><th>Miejsca</th><th>Akcja</th></tr>
                <tr><td>RZ12345</td><td>Mercedes Sprinter</td><td>20</td><td><button>Edytuj</button></td><td><button>Usuń</button></td></tr>
            </table>
        </div>
    </section>

    <!-- Przewoźnicy -->
    <section class="tab-content hidden" id="przewoznicy">
        <h2>Zarządzanie przewoźnikami</h2>
        <form method="POST" action="{{ route('admin.carriers.store') }}">
            @csrf
            <input type="text" name="name" placeholder="Nazwa przewoźnika" required />
            <input type="text" name="phone" placeholder="Telefon kontaktowy" />
            <input type="url" name="website" placeholder="Strona internetowa" />
            <button type="submit">Dodaj przewoźnika</button>
        </form>

        <div class="admin-list">
            <table>
                <tr><th>Nazwa</th><th>Telefon</th><th>WWW</th><th>Akcja</th></tr>
                <tr><td>FlixBus</td><td>+48 123 456 789</td><td><a href="#">flixbus.pl</a></td><td><button>Edytuj</button></td><td><button>Usuń</button></td></tr>
            </table>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Zakładki
        const buttons = document.querySelectorAll(".tab-button");
        const contents = document.querySelectorAll(".tab-content");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                buttons.forEach(b => b.classList.remove("active"));
                contents.forEach(c => c.classList.add("hidden"));
                button.classList.add("active");
                document.getElementById(button.dataset.tab).classList.remove("hidden");
            });
        });

        // Dodawanie przystanków
        const dodajBtn = document.getElementById("dodaj-przystanek");
        const wrapper = document.getElementById("przystanki-wrapper");

        dodajBtn.addEventListener("click", () => {
            const container = document.createElement("div");
            container.classList.add("przystanek-blok");
            container.innerHTML = `
                <input type="text" name="przystanki[]" placeholder="Nazwa przystanku" required />
                <input type="time" name="godziny[]" placeholder="Godzina" required />
                <button type="button" class="usun-przystanek">X</button>
            `;
            container.querySelector(".usun-przystanek").addEventListener("click", () => {
                container.remove();
            });
            wrapper.appendChild(container);
        });

        // Toggle visibility dla przystanków
        document.querySelectorAll(".toggle-stops").forEach(btn => {
            btn.addEventListener("click", () => {
                const row = btn.closest("tr").nextElementSibling;
                row.classList.toggle("hidden");
                btn.textContent = row.classList.contains("hidden") ? "Pokaż przystanki" : "Ukryj przystanki";
            });
        });
    </script>
@endsection
