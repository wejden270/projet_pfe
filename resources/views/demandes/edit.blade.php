@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Modifier la Demande #{{ $demande->id }}</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('demandes.update', $demande->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="en_attente" {{ $demande->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="acceptee" {{ $demande->status === 'acceptee' ? 'selected' : '' }}>Acceptée</option>
                                <option value="refusee" {{ $demande->status === 'refusee' ? 'selected' : '' }}>Refusée</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('demandes.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
