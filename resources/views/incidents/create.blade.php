<!DOCTYPE html>
<html>
<head>
    <title>Report Incident</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Report Incident</h1>
        <form action="{{ route('incidents.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id">User</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="car_id">Car</label>
                <select class="form-control" id="car_id" name="car_id" required>
                    @foreach($cars as $car)
                        <option value="{{ $car->id }}">{{ $car->license_plate }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="open" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Report Incident</button>
        </form>
    </div>
</body>
</html>
