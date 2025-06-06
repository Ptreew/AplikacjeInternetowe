@extends('layouts.app')

@section('title', 'Dodaj Nowe Miasto')

@section('header', 'Dodaj Nowe Miasto')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">Powrót do Listy Miast</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formularz Dodawania Miasta</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cities.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nazwa miasta</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="voivodeship" class="form-label">Województwo</label>
                        <input type="text" class="form-control" id="voivodeship" name="voivodeship" value="{{ old('voivodeship') }}" required>
                        @error('voivodeship')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    

                    
                    <button type="submit" class="btn btn-primary">Dodaj Miasto</button>
                </form>
            </div>
        </div>
    </div>
@endsection
