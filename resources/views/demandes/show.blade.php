@extends('layouts.app')

@section('content')
<!-- Font Awesome + Bootstrap 5 (si pas déjà inclus dans app layout) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails de la Demande #{{ $demande->id }}</h4>
                    <a href="{{ route('demandes.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-secondary"><i class="fas fa-user me-2"></i>Client</h5>
                        <ul class="list-unstyled ms-3">
                            <li><strong>Nom :</strong> {{ $demande->client->name }}</li>
                            <li><strong>Email :</strong> {{ $demande->client->email }}</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-secondary"><i class="fas fa-car me-2"></i>Chauffeur</h5>
                        <ul class="list-unstyled ms-3">
                            <li><strong>Nom :</strong> {{ $demande->chauffeur->name }}</li>
                            <li><strong>Email :</strong> {{ $demande->chauffeur->email }}</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-secondary"><i class="fas fa-flag me-2"></i>Status</h5>
                        <span class="badge bg-{{
                            $demande->status === 'acceptee' ? 'success' :
                            ($demande->status === 'refusee' ? 'danger' : 'warning')
                        }} text-uppercase px-3 py-2 fs-6">
                            {{ ucfirst($demande->status) }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-secondary"><i class="fas fa-clock me-2"></i>Dates</h5>
                        <ul class="list-unstyled ms-3">
                            <li><strong>Créée le :</strong> {{ $demande->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Mise à jour :</strong> {{ $demande->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('demandes.edit', $demande->id) }}" class="btn btn-outline-warning me-2">
                            <i class="fas fa-edit"></i> Éditer
                        </a>
                        <a href="{{ route('demandes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
