@extends('layouts.app')

@section('title', 'Zarządzanie przewoźnikami')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=przewoznicy') }}" class="btn btn-primary">Powrót do panelu</a>
                <a href="{{ route('admin.carriers.create') }}" class="btn btn-success">Dodaj nowego przewoźnika</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Lista przewoźników</h5>
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
                                <th>ID</th>
                                <th>Nazwa</th>
                                <th>Email</th>
                                <th>Strona internetowa</th>
                                <th>Akcje</th>
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
                                        <div class="d-inline-flex">
                                            <a href="{{ route('admin.carriers.edit', $carrier) }}" class="btn btn-sm btn-primary me-1">
                                                Edytuj
                                            </a>
                                            <a href="{{ route('admin.carriers.show', $carrier) }}" class="btn btn-sm btn-success me-1">
                                                Pokaż
                                            </a>
                                            <form action="{{ route('admin.carriers.destroy', $carrier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego przewoźnika?')">
                                                    Usuń
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
