@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Dodaj nowy bilet</h2>
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-primary">Powrót do listy biletów</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tickets.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Użytkownik <span class="text-danger">*</span></label>
                        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Wybierz użytkownika</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="departure_id" class="form-label">Odjazd <span class="text-danger">*</span></label>
                        <select id="departure_id" name="departure_id" class="form-select @error('departure_id') is-invalid @enderror" required>
                            <option value="">Wybierz odjazd</option>
                            @foreach($departures as $departure)
                                @php
                                    $route = $departure->schedule->route;
                                    $line = $route->line;
                                    $lineName = $line->number ? "Linia {$line->number}" : $line->name;
                                    $departureLabel = "{$lineName}: {$route->name} - {$departure->departure_time->format('d.m.Y H:i')}";
                                @endphp
                                <option value="{{ $departure->id }}" {{ old('departure_id') == $departure->id ? 'selected' : '' }}>
                                    {{ $departureLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('departure_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="passenger_name" class="form-label">Imię i nazwisko pasażera <span class="text-danger">*</span></label>
                        <input type="text" id="passenger_name" name="passenger_name" 
                               class="form-control @error('passenger_name') is-invalid @enderror" 
                               value="{{ old('passenger_name') }}" required>
                        @error('passenger_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="passenger_email" class="form-label">Email pasażera <span class="text-danger">*</span></label>
                        <input type="email" id="passenger_email" name="passenger_email" 
                               class="form-control @error('passenger_email') is-invalid @enderror" 
                               value="{{ old('passenger_email') }}" required>
                        @error('passenger_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="passenger_phone" class="form-label">Telefon pasażera</label>
                        <input type="text" id="passenger_phone" name="passenger_phone" 
                               class="form-control @error('passenger_phone') is-invalid @enderror" 
                               value="{{ old('passenger_phone') }}">
                        @error('passenger_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Status biletu <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Zarezerwowany</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Opłacony</option>
                            <option value="used" {{ old('status') == 'used' ? 'selected' : '' }}>Wykorzystany</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Anulowany</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Uwagi</label>
                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">Bilet aktywny</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Zapisz bilet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
