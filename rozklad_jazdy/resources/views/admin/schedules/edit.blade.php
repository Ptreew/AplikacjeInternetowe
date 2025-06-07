@extends('layouts.app')

@section('title', 'Edytuj rozkład')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do listy rozkładów
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edytuj rozkład jazdy #{{ $schedule->id }}</h5>
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

            <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="route_id" class="form-label">Trasa <span class="text-danger">*</span></label>
                        <select class="form-select @error('route_id') is-invalid @enderror" id="route_id" name="route_id" required>
                            <option value="">Wybierz trasę</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id) == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }} - {{ $route->line->name }}
                                    ({{ $route->type == 'city' ? 'Miejska' : 'Międzymiastowa' }})
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="valid_from" class="form-label">Data rozpoczęcia <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                               id="valid_from" name="valid_from" value="{{ old('valid_from', $schedule->valid_from ? date('Y-m-d', strtotime($schedule->valid_from)) : '') }}" required>
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="valid_to" class="form-label">Data zakończenia <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('valid_to') is-invalid @enderror" 
                               id="valid_to" name="valid_to" value="{{ old('valid_to', $schedule->valid_to ? date('Y-m-d', strtotime($schedule->valid_to)) : '') }}" required>
                        @error('valid_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Dni tygodnia <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-1" name="days_of_week[]" value="1" 
                                       {{ in_array(1, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-1">Poniedziałek</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-2" name="days_of_week[]" value="2" 
                                       {{ in_array(2, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-2">Wtorek</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-3" name="days_of_week[]" value="3" 
                                       {{ in_array(3, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-3">Środa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-4" name="days_of_week[]" value="4" 
                                       {{ in_array(4, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-4">Czwartek</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-5" name="days_of_week[]" value="5" 
                                       {{ in_array(5, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-5">Piątek</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-6" name="days_of_week[]" value="6" 
                                       {{ in_array(6, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-6">Sobota</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="day-0" name="days_of_week[]" value="0" 
                                       {{ in_array(0, old('days_of_week', $schedule->days_of_week)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day-0">Niedziela</label>
                            </div>
                        </div>
                        @error('days_of_week')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Zapisz zmiany
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Walidacja dat - data zakończenia musi być późniejsza niż data rozpoczęcia
        const validFromEl = document.getElementById('valid_from');
        const validToEl = document.getElementById('valid_to');
        
        validFromEl.addEventListener('change', function() {
            if (validToEl.value && validFromEl.value > validToEl.value) {
                validToEl.value = validFromEl.value;
            }
            validToEl.min = validFromEl.value;
        });
        
        validFromEl.dispatchEvent(new Event('change'));
    });
</script>
@endsection
