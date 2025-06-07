@extends('layouts.app')

@section('title', 'Zarządzanie przewoźnikami')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=przewoznicy') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do panelu</a>
                <a href="{{ route('admin.carriers.create') }}" class="btn btn-success"><i class="fas fa-plus me-2"></i>Dodaj nowego przewoźnika</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Lista przewoźników</h5>
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
                                <th style="width: 20%">Nazwa</th>
                                <th style="width: 20%">Email</th>
                                <th style="width: 25%">Strona internetowa</th>
                                <th style="width: 20%">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carriers as $carrier)
                                <tr>
                                    <td>{{ $carrier->id }}</td>
                                    <td>{{ $carrier->name }}</td>
                                    <td>{{ $carrier->email }}</td>
                                    <td>
                                        <a href="{{ $carrier->website }}" target="_blank">{{ $carrier->website }}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-nowrap">
                                            <a href="{{ route('admin.carriers.edit', $carrier) }}" class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit me-2"></i>Edytuj
                                            </a>
                                            <a href="{{ route('admin.carriers.show', $carrier) }}" class="btn btn-sm btn-success me-1">
                                                <i class="fas fa-info-circle me-2"></i>Szczegóły
                                            </a>
                                            <form action="{{ route('admin.carriers.destroy', $carrier) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego przewoźnika?')">
                                                    <i class="fas fa-trash-alt me-2"></i>Usuń
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Brak przewoźników w bazie danych</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $carriers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
