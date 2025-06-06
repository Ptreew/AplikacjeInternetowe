@extends('layouts.app')

@section('title', 'Moje konto')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Zarządzanie kontem</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <ul class="nav nav-tabs card-header-tabs" id="accountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ session('activeTab') == 'profile' || !session('activeTab') ? 'active' : 'text-white' }}" 
                       id="profile-tab" 
                       data-bs-toggle="tab" 
                       href="#profile" 
                       role="tab" 
                       aria-controls="profile" 
                       aria-selected="{{ session('activeTab') == 'profile' || !session('activeTab') ? 'true' : 'false' }}">
                        <i class="fas fa-user me-2"></i>Profil
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ session('activeTab') == 'password' ? 'active' : 'text-white' }}" 
                       id="password-tab" 
                       data-bs-toggle="tab" 
                       href="#password" 
                       role="tab" 
                       aria-controls="password" 
                       aria-selected="{{ session('activeTab') == 'password' ? 'true' : 'false' }}">
                        <i class="fas fa-key me-2"></i>Hasło
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ session('activeTab') == 'delete' ? 'active' : 'text-white' }}" 
                       id="delete-tab" 
                       data-bs-toggle="tab" 
                       href="#delete" 
                       role="tab" 
                       aria-controls="delete" 
                       aria-selected="{{ session('activeTab') == 'delete' ? 'true' : 'false' }}">
                        <i class="fas fa-trash me-2"></i>Usuń konto
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="accountTabsContent">
                <!-- Profile Tab -->
                <div class="tab-pane fade {{ session('activeTab') == 'profile' || !session('activeTab') ? 'show active' : '' }}" 
                     id="profile" 
                     role="tabpanel" 
                     aria-labelledby="profile-tab">
                    <h4>Twój profil</h4>
                    <form action="{{ route('account.update-profile') }}" method="POST" class="mt-3">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Imię i nazwisko</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                    </form>
                </div>
                
                <!-- Password Tab -->
                <div class="tab-pane fade {{ session('activeTab') == 'password' ? 'show active' : '' }}" 
                     id="password" 
                     role="tabpanel" 
                     aria-labelledby="password-tab">
                    <h4>Zmień hasło</h4>
                    <form action="{{ route('account.update-password') }}" method="POST" class="mt-3">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Aktualne hasło</label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nowe hasło</label>
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" 
                                   name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Potwierdź nowe hasło</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Zmień hasło</button>
                    </form>
                </div>
                
                <!-- Delete Account Tab -->
                <div class="tab-pane fade {{ session('activeTab') == 'delete' ? 'show active' : '' }}" 
                     id="delete" 
                     role="tabpanel" 
                     aria-labelledby="delete-tab">
                    <h4 class="text-danger">Usuń konto</h4>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Uwaga!</strong> Usunięcie konta jest nieodwracalne. Wszystkie dane zostaną trwale usunięte.
                    </div>
                    <form action="{{ route('account.destroy') }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Potwierdź hasło</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input @error('confirm_deletion') is-invalid @enderror" 
                                   id="confirm_deletion" 
                                   name="confirm_deletion" 
                                   value="1">
                            <label class="form-check-label" for="confirm_deletion">
                                Potwierdzam, że chcę usunąć moje konto i rozumiem konsekwencje tej operacji.
                            </label>
                            @error('confirm_deletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć swoje konto? Ta operacja jest nieodwracalna!')">
                            Usuń konto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Transfer tab active state from server-side to client-side
        const activeTab = '{{ session('activeTab') }}';
        if (activeTab) {
            const tab = document.querySelector(`#${activeTab}-tab`);
            if (tab) {
                const tabInstance = new bootstrap.Tab(tab);
                tabInstance.show();
            }
        }
        
        // Fix tab navigation styles on click
        const tabs = document.querySelectorAll('#accountTabs .nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active and add text-white to all tabs
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.classList.add('text-white');
                });
                
                // Add active and remove text-white from clicked tab
                this.classList.add('active');
                this.classList.remove('text-white');
            });
        });
    });
</script>
@endsection
