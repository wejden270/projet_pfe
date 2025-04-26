@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Liste des Demandes</h3>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
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
                                    <span class="badge badge-{{ $demande->status === 'acceptee' ? 'success' : ($demande->status === 'refusee' ? 'danger' : 'warning') }}">
                                        {{ $demande->status }}
                                    </span>
                                </td>
                                <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('demandes.show', $demande->id) }}" class="btn btn-info btn-sm">Voir</a>
                                    <a href="{{ route('demandes.edit', $demande->id) }}" class="btn btn-warning btn-sm">Éditer</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
