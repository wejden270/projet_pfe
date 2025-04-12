<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    // 🔹 Récupère toutes les localisations avec la voiture associée pour l'API
    public function index()
    {
        $locations = Location::with('car')->get();
        return response()->json($locations, 200);
    }

    // 🔹 Récupère toutes les localisations pour l'affichage dans une vue
    public function indexView()
    {
        $locations = Location::with('car')->get();
        return view('locations.index', compact('locations'));
    }

    // 🔹 Affiche le formulaire de création
    public function create()
    {
        return view('locations.create');
    }

    // 🔹 Enregistre une nouvelle localisation
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'timestamp' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        unset($validatedData['updated_at']);

        try {
            $location = Location::create($validatedData);
            return response()->json([
                'message' => 'Location created successfully.',
                'location' => $location
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create location.', 'details' => $e->getMessage()], 500);
        }
    }

    // 🔹 Affiche une localisation spécifique
    public function show(Location $location)
    {
        return response()->json($location, 200);
    }

    // 🔹 Affiche le formulaire d'édition
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    // 🔹 Met à jour une localisation
    public function update(Request $request, Location $location)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'timestamp' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        unset($validatedData['updated_at']);

        try {
            $location->update($validatedData);
            return response()->json([
                'message' => 'Location updated successfully.',
                'location' => $location
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update location.', 'details' => $e->getMessage()], 500);
        }
    }

    // 🔹 Supprime une localisation
    public function destroy(Location $location)
    {
        try {
            $location->delete();
            return response()->json(['message' => 'Location deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete location.', 'details' => $e->getMessage()], 500);
        }
    }
}
