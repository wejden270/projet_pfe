<!DOCTYPE html>
<html>
<head>
    <title>Car Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Car Details</h1>
        <p><strong>Make:</strong> {{ $car->make }}</p>
        <p><strong>Model:</strong> {{ $car->model }}</p>
        <p><strong>Year:</strong> {{ $car->year }}</p>
        <p><strong>License Plate:</strong> {{ $car->license_plate }}</p>
        <p><strong>User:</strong> {{ $car->user->name }}</p>
        <a href="{{ route('cars.index') }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>
