@extends('layouts.app')

@section('title', 'Kreator trasy - Krok 1')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.routes.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Powrót do listy tras
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-magic me-2"></i>Kreator trasy - Krok 1 z 3: Podstawowe informacje</h5>
                <span>1/3</span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">33%</div>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.routes.builder.step1.process') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type" class="form-label"><i class="fas fa-map-signs me-2"></i>Typ trasy <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Wybierz typ</option>
                                <option value="city" {{ old('type') == 'city' ? 'selected' : '' }}>Miejska</option>
                                <option value="intercity" {{ old('type') == 'intercity' ? 'selected' : '' }}>Międzymiastowa</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Określa czy trasa jest miejska czy międzymiastowa.</div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="line_id" class="form-label"><i class="fas fa-bus me-2"></i>Linia <span class="text-danger">*</span></label>
                            <select class="form-select @error('line_id') is-invalid @enderror" id="line_id" name="line_id" required>
                                <option value="">Wybierz linię</option>
                                @foreach($lines as $line)
                                    @php
                                        $hasVehicles = in_array($line->id, $linesWithVehicles);
                                    @endphp
                                    <option 
                                        value="{{ $line->id }}" 
                                        {{ old('line_id') == $line->id ? 'selected' : '' }}
                                        {{ !$hasVehicles ? 'disabled' : '' }}
                                        class="{{ !$hasVehicles ? 'text-muted' : '' }}"
                                        title="{{ !$hasVehicles ? 'Ta linia nie ma przypisanych pojazdów' : 'Linia ma przypisane pojazdy' }}"
                                    >
                                        {{ $line->carrier->name }} - 
                                        @if($line->number)
                                            Linia {{ $line->number }}
                                        @else
                                            Kurs międzymiastowy
                                        @endif
                                        - {{ $line->name }}
                                        {{ !$hasVehicles ? ' (brak pojazdów)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('line_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label"><i class="fas fa-tag me-2"></i>Nazwa trasy <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Na przykład: "Warszawa - Kraków" lub "Linia 175: Rondo Daszyńskiego - Lotnisko Okęcie"</div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Pole szacowanego czasu zostało usunięte - wartość jest teraz obliczana automatycznie -->
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Trasa aktywna
                            </label>
                            <div class="form-text">Odznacz, jeśli trasa jest tymczasowo nieaktywna.</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.routes.builder.cancel') }}" class="btn btn-secondary" id="cancel-button">
                        <i class="fas fa-times me-2"></i>Anuluj
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Dalej: Dodaj przystanki <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Czyszczenie localStorage przy anulowaniu buildera
        document.getElementById('cancel-button').addEventListener('click', function() {
            localStorage.removeItem('route_builder_city_stops');
            localStorage.removeItem('route_builder_intercity_stops');
            console.log('Wyczyszczono localStorage po anulowaniu buildera');
        });
        const lineSelect = document.getElementById('line_id');
        const typeSelect = document.getElementById('type');
        
        // Zapisz pierwotne opcje linii
        const originalOptions = Array.from(lineSelect.options);
        
        // Funkcja filtrująca linie na podstawie typu
        function filterLinesByType() {
            const selectedType = typeSelect.value;
            
            // Usuń wszystkie opcje oprócz pierwszej (placeholder "Wybierz linię")
            while (lineSelect.options.length > 1) {
                lineSelect.remove(1);
            }
            
            // Jeśli nie wybrano typu, nie pokazuj żadnych linii
            if (!selectedType) {
                return;
            }
            
            // Dodaj odpowiednie opcje w zależności od typu
            originalOptions.forEach(option => {
                if (option.value === '') return; // Pomiń opcję placeholder
                
                const isCity = option.text.includes('Linia');
                const isIntercity = option.text.includes('Kurs międzymiastowy');
                
                if ((selectedType === 'city' && isCity) || 
                    (selectedType === 'intercity' && isIntercity)) {
                    lineSelect.add(option.cloneNode(true));
                }
            });
        }
        
        // Automatycznie dostosuj typ trasy na podstawie wybranej linii
        lineSelect.addEventListener('change', function() {
            const lineOption = this.options[this.selectedIndex];
            if (!lineOption || lineOption.value === '') return;
            
            const lineText = lineOption.text;
            
            if (lineText.includes('Kurs międzymiastowy')) {
                typeSelect.value = 'intercity';
            } else if (lineText.includes('Linia')) {
                typeSelect.value = 'city';
            }
        });
        
        // Filtruj linie gdy zmienia się typ
        typeSelect.addEventListener('change', filterLinesByType);
        
        // Inicjalne filtrowanie
        filterLinesByType();
    });
</script>
@endsection
@endsection
