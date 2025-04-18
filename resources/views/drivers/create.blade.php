<!-- resources/views/drivers/create.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Chauffeur</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Ajouter un Chauffeur</h1>

        <!-- Formulaire d'ajout d'un chauffeur -->
        <form action="{{ route('drivers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="model">Modèle du véhicule</label>
                <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}">
                @error('model')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="license_plate">Immatriculation</label>
                <input type="text" class="form-control @error('license_plate') is-invalid @enderror" id="license_plate" name="license_plate" value="{{ old('license_plate') }}">
                @error('license_plate')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe :</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

        <!-- Affichage des erreurs -->
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS et dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
