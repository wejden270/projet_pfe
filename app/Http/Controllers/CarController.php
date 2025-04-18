<?php
namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with('user')->get(); // Chargez les voitures avec les informations de l'utilisateur
        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        $drivers = Driver::all(); // Récupérer les chauffeurs au lieu des utilisateurs
        return view('cars.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'make' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'license_plate' => 'required',
        ]);

        Car::create($request->all());
        return redirect()->route('cars.index')->with('success', 'Car created successfully.');
    }

    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $users = User::all(); // Récupérez tous les utilisateurs pour les passer à la vue
        return view('cars.edit', compact('car', 'users'));
    }

    public function update(Request $request, Car $car)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'make' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'license_plate' => 'required',
        ]);

        $car->update($request->all());
        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }
}
