<!DOCTYPE html>
<html>
<head>
    <title>Add Car</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Add Car</h1>
        <form action="{{ route('cars.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="driver_id">Chauffeur</label>
                <select name="driver_id" id="driver_id" class="form-control @error('driver_id') is-invalid @enderror">
                    <option value="">Sélectionner un chauffeur</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="make">Make</label>
                <input type="text" class="form-control" id="make" name="make" required>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" class="form-control" id="model" name="model" required>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" class="form-control" id="year" name="year" required>
            </div>
            <div class="form-group">
                <label for="license_plate">License Plate</label>
                <input type="text" class="form-control" id="license_plate" name="license_plate" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Car</button>
        </form>
    </div>
</body>
</html>
