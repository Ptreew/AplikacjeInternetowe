@extends('layouts.app')

@section('title', 'Dodaj Nowe Miasto')

@section('header', 'Dodaj Nowe Miasto')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Powrót do Listy Miast
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-city me-2"></i>Formularz Dodawania Miasta</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cities.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="fas fa-tag me-2"></i>Nazwa miasta</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="voivodeship" class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Województwo</label>
                        <input type="text" class="form-control" id="voivodeship" name="voivodeship" value="{{ old('voivodeship') }}" required>
                        @error('voivodeship')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    

                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Dodaj Miasto
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
