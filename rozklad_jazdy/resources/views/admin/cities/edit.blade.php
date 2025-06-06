@extends('layouts.app')

@section('title', 'Edytuj Miasto')

@section('header', 'Edytuj Miasto: ' . $city->name)

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">Powrót do Listy Miast</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formularz Edycji Miasta</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $city->name) }}" placeholder="Nazwa miasta" required />
                            <label for="name">Nazwa miasta</label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="voivodeship" name="voivodeship" value="{{ old('voivodeship', $city->voivodeship) }}" placeholder="Województwo" required />
                            <label for="voivodeship">Województwo</label>
                            @error('voivodeship')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Zaktualizuj Miasto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
