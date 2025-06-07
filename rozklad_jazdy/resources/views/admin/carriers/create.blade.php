@extends('layouts.app')

@section('title', 'Dodaj nowego przewoźnika')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.carriers.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Powrót do listy przewoźników</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Dodaj nowego przewoźnika</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close d-flex align-items-center justify-content-center" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

                <form action="{{ route('admin.carriers.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label"><i class="fas fa-bus me-2"></i>Nazwa przewoźnika</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website" class="form-label"><i class="fas fa-globe me-2"></i>Strona internetowa</label>
                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" required placeholder="https://example.com">
                        <div class="form-text">Adres powinien zaczynać się od https:// lub http://</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Dodaj przewoźnika</button>
                </form>
            </div>
        </div>
    </div>
@endsection
