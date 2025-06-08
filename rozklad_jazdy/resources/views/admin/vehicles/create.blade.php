@extends('layouts.app')

@section('title', 'Dodaj nowy pojazd')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do listy pojazdów</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bus me-2"></i>Dodaj nowy pojazd</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="vehicle_number" class="form-label"><i class="fas fa-hashtag me-2"></i>Numer pojazdu</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label"><i class="fas fa-truck-moving me-2"></i>Typ pojazdu</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="" disabled selected>Wybierz typ pojazdu</option>
                            <option value="Pociąg ekspresowy" {{ old('type') == 'Pociąg ekspresowy' ? 'selected' : '' }}>Pociąg ekspresowy</option>
                            <option value="Pociąg regionalny" {{ old('type') == 'Pociąg regionalny' ? 'selected' : '' }}>Pociąg regionalny</option>
                            <option value="Autokar" {{ old('type') == 'Autokar' ? 'selected' : '' }}>Autokar</option>
                            <option value="Autobus miejski" {{ old('type') == 'Autobus miejski' ? 'selected' : '' }}>Autobus miejski</option>
                            <option value="Autobus przegubowy" {{ old('type') == 'Autobus przegubowy' ? 'selected' : '' }}>Autobus przegubowy</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="line_id" class="form-label"><i class="fas fa-route me-2"></i>Przypisana linia</label>
                        <select class="form-select" id="line_id" name="line_id" required>
                            <option value="" disabled selected>Wybierz linię</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->id }}" {{ old('line_id') == $line->id ? 'selected' : '' }}>
                                    {{ $line->name }} (Przewoźnik: {{ $line->carrier->name ?? 'Brak' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="capacity" class="form-label"><i class="fas fa-users me-2"></i>Pojemność</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktywny</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label"><i class="fas fa-image me-2"></i>Zdjęcie pojazdu</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">Dozwolone formaty: JPG, PNG, GIF. Maksymalny rozmiar: 2MB</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Dodaj pojazd</button>
                </form>
            </div>
        </div>
    </div>
@endsection
