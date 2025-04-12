<!DOCTYPE html>
<html>
<head>
    <title>Add Location</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Add Location</h1>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="car_id">Car</label>
                <select class="form-control" id="car_id" name="car_id" required>
                    @foreach($cars as $car)
                        <option value="{{ $car->id }}">{{ $car->license_plate }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control" id="latitude" name="latitude" required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" class="form-control" id="longitude" name="longitude" required>
            </div>
            <div class="form-group">
                <label for="timestamp">Timestamp</label>
                <input type="datetime-local" class="form-control" id="timestamp" name="timestamp" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Location</button>
        </form>
    </div>
</body>
</html>
