@extends('layouts.app')

@section('title', 'Dodaj nową trasę')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=routes" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do panelu
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Dodaj nową trasę</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.routes.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="line_id" class="form-label">Linia <span class="text-danger">*</span></label>
                        <select class="form-select @error('line_id') is-invalid @enderror" id="line_id" name="line_id" required>
                            <option value="">Wybierz linię</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->id }}" {{ old('line_id') == $line->id ? 'selected' : '' }}>
                                    {{ $line->carrier->name }} - 
                                    @if($line->number)
                                        Linia {{ $line->number }}
                                    @else
                                        Kurs międzymiastowy
                                    @endif
                                    - {{ $line->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('line_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nazwa trasy <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Typ trasy <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Wybierz typ</option>
                            <option value="city" {{ old('type') == 'city' ? 'selected' : '' }}>Miejska</option>
                            <option value="intercity" {{ old('type') == 'intercity' ? 'selected' : '' }}>Międzymiastowa</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="travel_time" class="form-label">Czas podróży (minuty)</label>
                        <input type="number" class="form-control @error('travel_time') is-invalid @enderror" id="travel_time" name="travel_time" value="{{ old('travel_time') }}" min="1">
                        @error('travel_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Szacowany czas podróży w minutach.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Trasa aktywna
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Zapisz trasę
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
