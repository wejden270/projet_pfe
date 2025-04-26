@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Détails de la Demande #{{ $demande->id }}</h3>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <h5>Client</h5>
                        <p>Nom: {{ $demande->client->name }}</p>
                        <p>Email: {{ $demande->client->email }}</p>
                    </div>

                    <div class="mb-3">
                        <h5>Chauffeur</h5>
                        <p>Nom: {{ $demande->chauffeur->name }}</p>
                        <p>Email: {{ $demande->chauffeur->email }}</p>
                    </div>

                    <div class="mb-3">
                        <h5>Status</h5>
                        <p>
                            <span class="badge badge-{{ $demande->status === 'acceptee' ? 'success' : ($demande->status === 'refusee' ? 'danger' : 'warning') }}">
                                {{ $demande->status }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <h5>Dates</h5>
                        <p>Créée le: {{ $demande->created_at->format('d/m/Y H:i') }}</p>
                        <p>Mise à jour: {{ $demande->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('demandes.edit', $demande->id) }}" class="btn btn-warning">Éditer</a>
                        <a href="{{ route('demandes.index') }}" class="btn btn-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
