@extends('layouts.app')

@section('title', 'Zarządzanie Liniami')

@section('header', 'Zarządzanie Liniami')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin') }}?tab=lines" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Powrót do panelu
            </a>
            <a href="{{ route('admin.lines.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Dodaj nową linię
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lista linii</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 10%">Numer</th>
                            <th style="width: 15%">Nazwa</th>
                            <th style="width: 20%">Przewoźnik</th>
                            <th style="width: 15%">Kolor</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 20%">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lines as $line)
                            <tr>
                                <td>{{ $line->id }}</td>
                                <td>
                            @if($line->number)
                                <span class="badge" style="background-color: {{ $line->color ?? '#6c757d' }}; color: #fff;">{{ $line->number }}</span>
                            @else
                                <span class="badge bg-secondary text-white"><i class="fas fa-route"></i> <span style="font-weight: bold;">IC</span></span>
                            @endif
                        </td>
                                <td>{{ $line->name }}</td>
                                <td>{{ $line->carrier->name ?? 'Brak przewoźnika' }}</td>
                                <td>
                                    <span class="color-box" style="background-color: {{ $line->color }}; display: inline-block; width: 20px; height: 20px; margin-right: 5px; border: 1px solid #ccc;"></span>
                                    {{ $line->color }}
                                </td>
                                <td>
                                    @if($line->is_active)
                                        <span class="badge bg-success">Aktywna</span>
                                    @else
                                        <span class="badge bg-danger">Nieaktywna</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-nowrap justify-content-center">
                                        <a href="{{ route('admin.lines.edit', $line) }}" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit me-1"></i>Edytuj
                                        </a>
                                        <a href="{{ route('admin.lines.show', $line) }}" class="btn btn-sm btn-success me-1">
                                            <i class="fas fa-eye me-1"></i>Pokaż
                                        </a>
                                        <form action="{{ route('admin.lines.destroy', $line) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę linię?')">
                                                <i class="fas fa-trash-alt me-1"></i>Usuń
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Brak linii w bazie danych</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $lines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
