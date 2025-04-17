<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Car;
use App\Models\User;
use App\Models\Driver;
use App\Models\Incident;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Redirige vers index.html après connexion
            return redirect('/index.html');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function showRegistrationForm()
    {
        return view('admin.register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->intended('/');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/');
    }

    public function dashboard()
    {
        $data = [
            'totalCars' => Car::count(),
            'activeIncidents' => Incident::where('status', 'active')->count(),
            'totalDrivers' => Driver::count(),
            'totalUsers' => User::count(),
            'recentIncidents' => Incident::latest()->take(6)->get(),
            'carStatuses' => [
                'available' => Car::where('status', 'available')->count(),
                'intervention' => Car::where('status', 'intervention')->count(),
                'maintenance' => Car::where('status', 'maintenance')->count(),
            ]
        ];

        return view('admin.dashboard', $data);
    }
}
