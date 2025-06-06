@extends('layouts.app')

@section('title', 'Zarządzanie użytkownikami')

@section('header', 'Zarządzanie użytkownikami')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/admin?tab=uzytkownicy') }}" class="btn btn-primary">Powrót do panelu</a>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Lista użytkowników</h5>
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
                                <th>Email</th>
                                <th>Nazwa użytkownika</th>
                                <th>Rola</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        @if($user->role == 'admin')
                                            <span class="badge bg-success">Administrator</span>
                                        @else
                                            <span class="badge bg-primary">Użytkownik</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="{{ $user->id == auth()->id() ? 'tooltip' : 'modal' }}" data-bs-target="{{ $user->id == auth()->id() ? '' : '#changeRoleModal'.$user->id }}" {{ $user->id == auth()->id() ? 'disabled' : '' }} title="{{ $user->id == auth()->id() ? 'Aktualny użytkownik' : '' }}">
                                                Zmień rolę
                                            </button>
                                            
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')" {{ $user->id == auth()->id() ? 'disabled' : '' }} data-bs-toggle="{{ $user->id == auth()->id() ? 'tooltip' : '' }}" title="{{ $user->id == auth()->id() ? 'Aktualny użytkownik' : '' }}">
                                                    Usuń
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Modal do zmiany roli -->
                                        <div class="modal fade" id="changeRoleModal{{ $user->id }}" tabindex="-1" aria-labelledby="changeRoleModalLabel{{ $user->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="changeRoleModalLabel{{ $user->id }}">Zmień rolę użytkownika</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="role" class="form-label">Rola</label>
                                                                <select class="form-select" id="role" name="role" required>
                                                                    <option value="standard" {{ $user->role == 'standard' ? 'selected' : '' }}>Użytkownik</option>
                                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                                                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
