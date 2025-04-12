<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with(['user', 'car'])->get();
        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        $users = User::all();
        $cars = Car::all();
        return view('incidents.create', compact('users', 'cars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'location' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        Incident::create($request->all());
        return redirect()->route('incidents.index')->with('success', 'Incident created successfully.');
    }

    public function show(Incident $incident)
    {
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $users = User::all();
        $cars = Car::all();
        return view('incidents.edit', compact('incident', 'users', 'cars'));
    }

    public function update(Request $request, Incident $incident)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'location' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $incident->update($request->all());
        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Incident deleted successfully.');
    }
}

