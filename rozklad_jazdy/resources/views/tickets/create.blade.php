@extends('layouts.app')

@section('title', 'Kup bilet')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Kup bilet</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Szczegóły połączenia:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-25">Linia</th>
                                        <td>{{ $departure->schedule->route->line->name ?? 'Brak danych' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Przewoźnik</th>
                                        <td>{{ $departure->schedule->route->line->carrier->name ?? 'Brak danych' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Trasa</th>
                                        <td>
                                            @php
                                                $stops = $departure->schedule->route->stops;
                                                $firstStop = $stops->first();
                                                $lastStop = $stops->last();
                                            @endphp
                                            {{ $firstStop->city->name ?? 'Brak danych' }} ({{ $firstStop->name }}) → 
                                            {{ $lastStop->city->name ?? 'Brak danych' }} ({{ $lastStop->name }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Data i godzina odjazdu</th>
                                        <td>{{ \Carbon\Carbon::parse(($travelDate ?? now()->toDateString()).' '.$departure->departure_time)->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Cena</th>
                                        <td class="h5 text-primary">{{ number_format($departure->price, 2) }} zł</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pojazd</th>
                                        <td>
                                            {{ $departure->vehicle->type ?? 'Brak danych' }}
                                            @if(!empty($departure->vehicle->vehicle_number))
                                                (nr {{ $departure->vehicle->vehicle_number }})
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Dane pasażera</h5>
                    
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="departure_id" value="{{ $departure->id }}">
                        <input type="hidden" name="travel_date" value="{{ $travelDate ?? request('travel_date') ?? now()->toDateString() }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="passenger_name" class="form-label">Imię i nazwisko <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('passenger_name') is-invalid @enderror" 
                                       id="passenger_name" name="passenger_name" 
                                       value="{{ old('passenger_name', Auth::user()->name ?? '') }}" required>
                                @error('passenger_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="passenger_email" class="form-label">Adres e-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('passenger_email') is-invalid @enderror" 
                                       id="passenger_email" name="passenger_email" 
                                       value="{{ old('passenger_email', Auth::user()->email ?? '') }}" required>
                                @error('passenger_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="passenger_phone" class="form-label">Telefon kontaktowy</label>
                                <input type="tel" class="form-control @error('passenger_phone') is-invalid @enderror" 
                                       id="passenger_phone" name="passenger_phone" 
                                       value="{{ old('passenger_phone') }}">
                                @error('passenger_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">Uwagi (opcjonalnie)</label>
                                <input type="text" class="form-control" id="notes" name="notes" 
                                       value="{{ old('notes') }}">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-md-2">
                                Powrót
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-ticket-alt me-1"></i> Zarezerwuj bilet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 1rem 1.5rem;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .table th {
        background-color: #f8f9fa;
    }
    
    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
</style>
@endsection
