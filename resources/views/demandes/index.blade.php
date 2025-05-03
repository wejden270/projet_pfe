@extends('layouts.app')

@section('content')
<!-- Font Awesome et Bootstrap 5 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="fas fa-list me-2"></i>Liste des Demandes</h2>
        <a href="/index.html" class="btn btn-secondary mr-2">
            <i class="fas fa-home"></i> Home
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body">
            @if($demandes->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                    Aucune demande trouvée.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Chauffeur</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandes as $demande)
                                <tr>
                                    <td>{{ $demande->id }}</td>
                                    <td>{{ $demande->client->name }}</td>
                                    <td>{{ $demande->chauffeur->name }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $demande->status === 'acceptee' ? 'success' :
                                            ($demande->status === 'refusee' ? 'danger' : 'warning')
                                        }} text-uppercase">
                                            {{ ucfirst($demande->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('demandes.show', $demande->id) }}" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('demandes.edit', $demande->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
