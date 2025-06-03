@extends('layouts.app')

@section('title', 'Edytuj przewoźnika')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.carriers.index') }}" class="btn btn-primary">Powrót do listy przewoźników</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edytuj przewoźnika: {{ $carrier->name }}</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.carriers.update', $carrier) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nazwa przewoźnika</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $carrier->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $carrier->email) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website" class="form-label">Strona internetowa</label>
                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $carrier->website) }}" required placeholder="https://example.com">
                        <div class="form-text">Adres powinien zaczynać się od https:// lub http://</div>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Zapisz zmiany</button>
                </form>
            </div>
        </div>
    </div>
@endsection
