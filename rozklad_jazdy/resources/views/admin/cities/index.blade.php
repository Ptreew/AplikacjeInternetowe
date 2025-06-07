@extends('layouts.app')

@section('title', 'Zarządzanie Miastami')

@section('header', 'Zarządzanie Miastami')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=cities') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-1"></i>Powrót do panelu</a>
                <a href="{{ route('admin.cities.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i>Dodaj Nowe Miasto</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Lista Miast</h5>
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
                                <th>Nazwa</th>
                                <th>Województwo</th>
                                <th>Liczba przystanków</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $city)
                                <tr>
                                    <td>{{ $city->id }}</td>
                                    <td>{{ $city->name }}</td>
                                    <td>{{ $city->voivodeship }}</td>
                                    <td>{{ $city->stops->count() }}</td>
                                    <td class="text-center">
                                        <div class="d-flex flex-nowrap justify-content-center">
                                            <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit me-1"></i>Edytuj
                                            </a>
                                            <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-eye me-1"></i>Szczegóły
                                            </a>
                                            <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć to miasto?')">
                                                    <i class="fas fa-trash-alt me-1"></i>Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $cities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
