<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 450px;
            margin-top: 50px;
        }
        h2 {
            color: #5f6368;
            font-weight: bold;
            text-align: center;
        }
        .form-control {
            border-radius: 25px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            padding: 12px 20px;
            border-radius: 25px;
            width: 100%;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .mt-3 {
            text-align: center;
        }
        .mt-3 a {
            color: #007bff;
        }
        .mt-3 a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Register</h2>
        <form action="{{ route('admin.register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password-confirm">Confirm Password</label>
                <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="{{ route('admin.login') }}">Login here</a></p>
    </div>
</body>
</html>
