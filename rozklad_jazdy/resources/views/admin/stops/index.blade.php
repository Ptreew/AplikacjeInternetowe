@extends('layouts.app')

@section('title', 'Zarządzanie Przystankami')

@section('header', 'Zarządzanie Przystankami')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=stops') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
                <a href="{{ route('admin.stops.create') }}" class="btn btn-success"><i class="fas fa-plus me-2"></i>Dodaj Nowy Przystanek</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-map-pin me-2"></i>Lista Przystanków</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nazwa przystanku</th>
                                <th>Kod</th>
                                <th>Miasto</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stops as $stop)
                                <tr>
                                    <td>{{ $stop->id }}</td>
                                    <td>{{ $stop->name }}</td>
                                    <td>{{ $stop->code }}</td>
                                    <td>{{ $stop->city->name }} ({{ $stop->city->voivodeship }})</td>
                                    <td class="text-center">
                                        @if($stop->is_active)
                                            <span class="badge bg-success">Aktywny</span>
                                        @else
                                            <span class="badge bg-danger">Nieaktywny</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-nowrap justify-content-center">
                                            <a href="{{ route('admin.stops.edit', $stop) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit me-1"></i>Edytuj
                                            </a>
                                            <form action="{{ route('admin.stops.destroy', $stop) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten przystanek?')">
                                                    <i class="fas fa-trash-alt me-1"></i>Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $stops->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
