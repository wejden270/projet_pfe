<!DOCTYPE html>
<html>
<head>
    <title>Edit Incident</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Incident</h1>
        <form action="{{ route('incidents.update', $incident->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="user_id">User</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user->id == $incident->user_id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="car_id">Car</label>
                <select class="form-control" id="car_id" name="car_id" required>
                    @foreach($cars as $car)
                        <option value="{{ $car->id }}" {{ $car->id == $incident->car_id ? 'selected' : '' }}>
                            {{ $car->license_plate }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="{{ $incident->location }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required>{{ $incident->description }}</textarea>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="{{ $incident->status }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Incident</button>
        </form>
    </div>
</body>
</html>
