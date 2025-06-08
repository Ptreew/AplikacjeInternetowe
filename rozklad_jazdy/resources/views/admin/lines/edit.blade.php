@extends('layouts.app')

@section('title', 'Edytuj linię')

@section('header', 'Edytuj linię')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=lines" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Powrót do panelu
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edycja linii #{{ $line->id }} - {{ $line->number }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lines.update', $line) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="carrier_id" class="form-label"><i class="fas fa-bus me-2"></i>Przewoźnik <span class="text-danger">*</span></label>
                        <select class="form-select @error('carrier_id') is-invalid @enderror" id="carrier_id" name="carrier_id" required>
                            <option value="">Wybierz przewoźnika</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ (old('carrier_id', $line->carrier_id) == $carrier->id) ? 'selected' : '' }}>
                                    {{ $carrier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="color" class="form-label"><i class="fas fa-palette me-2"></i>Kolor <span class="text-danger">*</span></label>
                        <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $line->color) }}" title="Wybierz kolor dla linii" required>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label"><i class="fas fa-tag me-2"></i>Nazwa linii <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $line->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $line->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktywna
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-map-signs me-2"></i>Typ linii <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <div class="form-check me-4">
                            <input class="form-check-input" type="radio" name="line_type" id="line_type_city" value="city" {{ $line->number ? 'checked' : '' }} onchange="toggleLineNumberField()">
                            <label class="form-check-label" for="line_type_city">Linia miejska</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="line_type" id="line_type_intercity" value="intercity" {{ !$line->number ? 'checked' : '' }} onchange="toggleLineNumberField()">
                            <label class="form-check-label" for="line_type_intercity">Kurs międzymiastowy</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="number_field_container">
                    <label for="number" class="form-label"><i class="fas fa-hashtag me-2"></i>Numer linii <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number', $line->number) }}" required>
                    @error('number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Zapisz zmiany
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleLineNumberField() {
        const lineTypeCity = document.getElementById('line_type_city');
        const numberField = document.getElementById('number');
        const numberFieldContainer = document.getElementById('number_field_container');
        
        if (lineTypeCity.checked) {
            numberFieldContainer.style.display = 'block';
            numberField.required = true;
        } else {
            numberFieldContainer.style.display = 'none';
            numberField.required = false;
            numberField.value = '';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleLineNumberField();
    });
</script>

@endsection
