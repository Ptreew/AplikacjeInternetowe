@extends('layouts.app')

@section('title', 'Logowanie')

@section('header', 'Logowanie')

{{-- Navigation is handled in the main layout --}}

@section('content')
    <section>
        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf
            <label for="login_id">Nazwa użytkownika lub e-mail:</label>
            <input type="text" id="login_id" name="login_id" placeholder="Wpisz nazwę użytkownika lub e-mail" value="{{ old('login_id') }}" required />
            @error('login_id')
                <span class="error">{{ $message }}</span>
            @enderror
            
            @error('login')
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
