@extends('layouts.app')

@section('title', 'Szczegóły przewoźnika')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.carriers.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do listy przewoźników</a>
                <a href="{{ route('admin.carriers.edit', $carrier) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edytuj</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Szczegóły przewoźnika: {{ $carrier->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4"><i class="fas fa-hashtag me-2"></i>ID:</dt>
                            <dd class="col-sm-8">{{ $carrier->id }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-tag me-2"></i>Nazwa:</dt>
                            <dd class="col-sm-8">{{ $carrier->name }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-envelope me-2"></i>Email:</dt>
                            <dd class="col-sm-8">{{ $carrier->email }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-globe me-2"></i>Strona internetowa:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ $carrier->website }}" target="_blank">{{ $carrier->website }}</a>
                            </dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-calendar-plus me-2"></i>Data utworzenia:</dt>
                            <dd class="col-sm-8">{{ $carrier->created_at->format('d.m.Y H:i') }}</dd>
                            
                            <dt class="col-sm-4"><i class="fas fa-calendar-check me-2"></i>Data aktualizacji:</dt>
                            <dd class="col-sm-8">{{ $carrier->updated_at->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-route me-2"></i>Linie przewoźnika</h5>
            </div>
            <div class="card-body">
                @if($carrier->lines->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa linii</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrier->lines as $line)
                                    <tr>
                                        <td>{{ $line->id }}</td>
                                        <td>{{ $line->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.lines.show', $line) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-info-circle me-2"></i>Szczegóły
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">Ten przewoźnik nie obsługuje jeszcze żadnych linii.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
