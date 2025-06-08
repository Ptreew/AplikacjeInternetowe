@extends('layouts.app')

@section('title', 'Dodaj Nowy Przystanek')

@section('header', 'Dodaj Nowy Przystanek')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.stops.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Powrót do Listy Przystanków
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Formularz Dodawania Przystanku</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.stops.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="city_id" name="city_id" required>
                                <option value="">Wybierz miasto</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }} ({{ $city->voivodeship }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="city_id"><i class="fas fa-city me-2"></i>Miasto</label>
                            @error('city_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nazwa przystanku" required />
                            <label for="name"><i class="fas fa-sign me-2"></i>Nazwa przystanku</label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" placeholder="Kod przystanku" required />
                            <label for="code"><i class="fas fa-route me-2"></i>Kod przystanku</label>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3 mt-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active"><i></i>Aktywny</label>
                            @error('is_active')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Dodaj Przystanek
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
