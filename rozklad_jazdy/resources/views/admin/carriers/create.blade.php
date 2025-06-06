@extends('layouts.app')

@section('title', 'Dodaj nowego przewoźnika')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.carriers.index') }}" class="btn btn-primary">Powrót do listy przewoźników</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dodaj nowego przewoźnika</h5>
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
                        <label for="name" class="form-label">Nazwa przewoźnika</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website" class="form-label">Strona internetowa</label>
                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" required placeholder="https://example.com">
                        <div class="form-text">Adres powinien zaczynać się od https:// lub http://</div>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Dodaj przewoźnika</button>
                </form>
            </div>
        </div>
    </div>
@endsection
