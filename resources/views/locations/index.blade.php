<!DOCTYPE html>
<html lang="en">
<head>
    <title>Locations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Locations List</h2>
            <div>
                <a href="/index.html" class="btn btn-secondary mr-2">
                    <i class="fas fa-home"></i> Main Dashboard
                </a>
                <a href="{{ route('locations.create') }}" class="btn btn-primary">Add Location</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Timestamp</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($locations as $location)
                    <tr>
                        <td>{{ $location->car->license_plate }}</td>
                        <td>{{ $location->latitude }}</td>
                        <td>{{ $location->longitude }}</td>
                        <td>{{ $location->timestamp }}</td>
                        <td>
                            <a href="{{ route('locations.show', $location->id) }}" class="btn btn-info">View</a>
                            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
