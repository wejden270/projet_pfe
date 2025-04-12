<!DOCTYPE html>
<html>
<head>
    <title>Location Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Location Details</h1>
        <p><strong>Car:</strong> {{ $location->car->license_plate }}</p>
        <p><strong>Latitude:</strong> {{ $location->latitude }}</p>
        <p><strong>Longitude:</strong> {{ $location->longitude }}</p>
        <p><strong>Timestamp:</strong> {{ $location->timestamp }}</p>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>
