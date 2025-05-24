@extends('layouts.app')

@section('title', 'Logowanie')

@section('header', 'Logowanie')

@section('navigation')
    <a href="{{ route('register') }}">Zarejestruj</a>
    <a href="{{ route('home') }}">Strona główna</a>
@endsection

@section('content')
    <section>
        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Login/email:</label>
            <input type="text" id="email" name="email" placeholder="Wpisz login lub email" required />
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror

            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" placeholder="Wpisz hasło" required />
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror

            <button type="submit">Zaloguj się</button>
            <p>Nie masz konta? <a href="{{ route('register') }}">Zarejestruj się</a></p>
        </form>
    </section>
@endsection
