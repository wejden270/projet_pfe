@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2>Détails du chauffeur</h2>
        </div>
        <div class="card-body">
            @if(isset($driver))
                <div class="mb-3">
                    <strong>Nom :</strong> {{ $driver->name }}
                </div>
                <div class="mb-3">
                    <strong>Email :</strong> {{ $driver->email }}
                </div>
            @else
                <div class="alert alert-danger">
                    Chauffeur introuvable.
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>
@endsection
