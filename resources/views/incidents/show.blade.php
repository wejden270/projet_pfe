<!DOCTYPE html>
<html>
<head>
    <title>Incident Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Incident Details</h1>
        <p><strong>User:</strong> {{ $incident->user->name }}</p>
        <p><strong>Car:</strong> {{ $incident->car->license_plate }}</p>
        <p><strong>Location:</strong> {{ $incident->location }}</p>
        <p><strong>Description:</strong> {{ $incident->description }}</p>
        <p><strong>Status:</strong> {{ $incident->status }}</p>
        <a href="{{ route('incidents.index') }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>
