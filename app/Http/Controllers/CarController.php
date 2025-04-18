<?php
namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with('driver')->get(); // Changé de 'user' à 'driver'
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

        $car = Car::create($request->all());

        // Mettre à jour le modèle et la plaque d'immatriculation du conducteur
        $driver = Driver::findOrFail($request->driver_id);
        $driver->update([
            'model' => $request->model,
            'license_plate' => $request->license_plate
        ]);

        return redirect()->route('cars.index')->with('success', 'Car created successfully.');
    }

    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $drivers = Driver::all(); // Changé de User::all() à Driver::all()
        return view('cars.edit', compact('car', 'drivers'));
    }

    public function update(Request $request, Car $car)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id', // Changé de user_id à driver_id
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
