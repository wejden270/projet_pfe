<!DOCTYPE html>
<html>
<head>
    <title>Incidents</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Incidents</h1>
        <a href="{{ route('incidents.create') }}" class="btn btn-primary mb-3">Report Incident</a>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Car</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incidents as $incident)
                    <tr>
                        <td>{{ $incident->user->name }}</td>
                        <td>{{ $incident->car->license_plate }}</td>
                        <td>{{ $incident->location }}</td>
                        <td>{{ $incident->description }}</td>
                        <td>{{ $incident->status }}</td>
                        <td>
                            <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-info">View</a>
                            <a href="{{ route('incidents.edit', $incident->id) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('incidents.destroy', $incident->id) }}" method="POST" style="display:inline;">
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
