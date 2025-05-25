@extends('layouts.app')

@section('title', 'Rejestracja')

@section('header', 'Rejestracja')

{{-- Navigation is handled in the main layout --}}

@section('content')
    <section>
        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Imię i nazwisko:</label>
            <input type="text" id="name" name="name" placeholder="Wpisz imię i nazwisko" required />
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror

            <label for="username">Login:</label>
            <input type="text" id="username" name="username" placeholder="Wpisz login" required />
            @error('username')
                <span class="error">{{ $message }}</span>
            @enderror

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Wpisz email" required />
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror

            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" placeholder="Wpisz hasło" required />
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror

            <label for="password_confirmation">Powtórz hasło:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Powtórz hasło" required />

            <button type="submit">Zarejestruj się</button>
            <p>Masz już konto? <a href="{{ route('login') }}">Zaloguj się</a></p>
        </form>
    </section>
@endsection
