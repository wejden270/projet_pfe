@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4 text-center">Modifier le chauffeur</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Nom :</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $driver->name }}" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email :</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $driver->email }}" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-bold">Téléphone :</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $driver->phone }}" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Modifier</button>
            </div>
        </form>
    </div>
</div>
@endsection
