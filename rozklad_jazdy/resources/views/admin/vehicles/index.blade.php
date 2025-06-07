@extends('layouts.app')

@section('title', 'Zarządzanie pojazdami')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=pojazdy') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
                <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success"><i class="fas fa-plus me-2"></i>Dodaj nowy pojazd</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bus-alt me-2"></i>Lista pojazdów</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">ID</th>
                                <th style="width: 10%">Numer pojazdu</th>
                                <th style="width: 10%">Typ</th>
                                <th style="width: 15%">Linia</th>
                                <th style="width: 15%">Przewoźnik</th>
                                <th style="width: 8%">Pojemność</th>
                                <th style="width: 8%">Status</th>
                                <th style="width: 17%">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->id }}</td>
                                    <td>{{ $vehicle->vehicle_number }}</td>
                                    <td>{{ $vehicle->type }}</td>
                                    <td>{{ $vehicle->line->name ?? 'Brak linii' }}</td>
                                    <td>{{ $vehicle->line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                    <td>{{ $vehicle->capacity }}</td>
                                    <td>
                                        @if($vehicle->is_active)
                                            <span class="badge bg-success">Aktywny</span>
                                        @else
                                            <span class="badge bg-danger">Nieaktywny</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-nowrap">
                                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit me-2"></i>Edytuj
                                            </a>
                                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-info-circle me-2"></i>Szczegóły
                                            </a>
                                            <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten pojazd?')">
                                                    <i class="fas fa-trash-alt me-2"></i>Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Brak pojazdów w bazie danych</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
