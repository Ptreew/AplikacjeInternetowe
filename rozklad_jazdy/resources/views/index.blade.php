@extends('layouts.app')

@section('title', 'Rozkład jazdy')

@section('navigation')
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Zarejestruj</a>
    <a href="{{ route('admin') }}">Panel administratora</a>
@endsection

@section('content')
    <div class="tabs">
        <button class="tab-button active" data-tab="miedzymiastowe">Międzymiastowe</button>
        <button class="tab-button" data-tab="miejskie">Miejskie</button>
    </div>

    <!-- Międzymiastowe -->
    <section class="tab-content" id="miedzymiastowe">
        <h2>Wyszukaj kurs międzymiastowy</h2>
        <form>
            <input type="text" placeholder="Z (np. Warszawa)" required>
            <input type="text" placeholder="Do (np. Kraków)" required>
            <input type="date" id="data-wyjazdu">
            <input type="time" id="godzina-wyjazdu">
            <button type="submit">Szukaj</button>
        </form>
        <div class="wynik-trasy" id="wynik-miedzymiastowe"></div>
    </section>

    <!-- Miejskie -->
    <section class="tab-content hidden" id="miejskie">
        <h2>Wyszukaj kurs miejski</h2>
        <form>
            <input type="text" placeholder="Przystanek początkowy" required>
            <input type="text" placeholder="Przystanek końcowy" required>
            <button type="submit">Szukaj</button>
        </form>
        <div class="wynik-trasy" id="wynik-miejskie"></div>
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
      
        // Aktualna data i godzina
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const yyyy = now.getFullYear();
        const mmDate = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
      
        document.getElementById("godzina-wyjazdu").value = `${hh}:${mm}`;
        document.getElementById("data-wyjazdu").value = `${yyyy}-${mmDate}-${dd}`;
      
        // Formularze
        document.querySelector("#miedzymiastowe form").addEventListener("submit", function(e) {
            e.preventDefault();
            document.getElementById("wynik-miedzymiastowe").innerHTML = `
                <h3>Przykładowa trasa:</h3>
                <p>Warszawa → Kraków</p>
                <p>Data: ${document.getElementById("data-wyjazdu").value}</p>
                <p>Godzina: ${document.getElementById("godzina-wyjazdu").value}</p>
                <p>Przewoźnik: PKS Express</p>
                `;
        });
      
        document.querySelector("#miejskie form").addEventListener("submit", function(e) {
            e.preventDefault();
            document.getElementById("wynik-miejskie").innerHTML = `
                <h3>Przykładowa trasa:</h3>
                <p>Przystanek: Rondo Dmowskiego → Przystanek: Aleje Jerozolimskie</p>
                <p>Linia: 175 | Czas przejazdu: ok. 18 min</p>
                `;
        });
    </script>
@endsection
