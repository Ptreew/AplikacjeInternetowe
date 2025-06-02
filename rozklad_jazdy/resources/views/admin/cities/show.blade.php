@extends('layouts.app')

@section('title', 'Szczegóły Miasta')

@section('header', 'Szczegóły Miasta: ' . $city->name)

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.cities.index') }}" class="btn btn-primary">Powrót do Listy Miast</a>
                <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-success">Edytuj Miasto</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informacje o Mieście</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9">{{ $city->id }}</dd>

                    <dt class="col-sm-3">Nazwa:</dt>
                    <dd class="col-sm-9">{{ $city->name }}</dd>

                    <dt class="col-sm-3">Województwo:</dt>
                    <dd class="col-sm-9">{{ $city->voivodeship }}</dd>

                    <dt class="col-sm-3">Liczba przystanków:</dt>
                    <dd class="col-sm-9">{{ $city->stops->count() }}</dd>

                    <dt class="col-sm-3">Utworzono:</dt>
                    <dd class="col-sm-9">{{ $city->created_at }}</dd>

                    <dt class="col-sm-3">Zaktualizowano:</dt>
                    <dd class="col-sm-9">{{ $city->updated_at }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Przystanki w Mieście</h5>
            </div>
            <div class="card-body">
                @if($city->stops->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa przystanku</th>
                                    <th>Kod</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($city->stops as $stop)
                                    <tr>
                                        <td>{{ $stop->id }}</td>
                                        <td>{{ $stop->name }}</td>
                                        <td>{{ $stop->code }}</td>
                                        <td>{{ $stop->is_active ? 'Aktywny' : 'Nieaktywny' }}</td>
                                        <td>
                                            <a href="{{ route('admin.stops.edit', $stop) }}" class="btn btn-sm btn-primary me-1">Edytuj</a>
                                            <form action="{{ route('admin.stops.destroy', $stop) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten przystanek?')">Usuń</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Brak przystanków przypisanych do tego miasta. 
                        <a href="{{ route('admin.stops.create') }}" class="alert-link">Dodaj pierwszy przystanek</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
