<!DOCTYPE html>
<html>
<head>
    <title>Cars</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Cars</h1>
        <a href="{{ route('cars.create') }}" class="btn btn-primary mb-3">Add Car</a>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>License Plate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cars as $car)
                    <tr>
                        <td>{{ $car->user ? $car->user->name : 'N/A' }}</td>
                        <td>{{ $car->make }}</td>
                        <td>{{ $car->model }}</td>
                        <td>{{ $car->year }}</td>
                        <td>{{ $car->license_plate }}</td>
                        <td>
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('cars.destroy', $car->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No cars available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
