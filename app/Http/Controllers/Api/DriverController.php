<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    // ✅ Afficher la liste des chauffeurs
    public function index()
    {
        $drivers = Driver::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Liste des chauffeurs',
            'data' => $drivers
        ]);
    }

    // ✅ Mettre à jour la position d’un chauffeur
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driver = Auth::user();

        if (!$driver || !$driver instanceof Driver) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $driver->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'disponible'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Position mise à jour avec succès.',
            'data' => $driver
        ]);
    }

    // ✅ Récupérer les chauffeurs proches d'une position donnée
    public function getNearbyDrivers(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $radius = 50; // Rayon en km

        $drivers = Driver::selectRaw(
            "id, name, email, latitude, longitude, status, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
            * cos(radians(longitude) - radians(?)) 
            + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$request->latitude, $request->longitude, $request->latitude]
        )
        ->having("distance", "<=", $radius)
        ->where('status', 'disponible')
        ->orderBy("distance", "asc")
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Chauffeurs disponibles à proximité',
            'data' => $drivers
        ]);
    }

    // ✅ Envoyer une demande à un chauffeur
    public function requestDriver(Request $request)
    {
        $request->validate([
            'client_latitude' => 'required|numeric',
            'client_longitude' => 'required|numeric',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $driver = Driver::find($request->driver_id);

        if (!$driver || $driver->status !== 'disponible') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce chauffeur n\'est pas disponible.'
            ], 400);
        }

        $serviceRequest = ServiceRequest::create([
            'client_latitude' => $request->client_latitude,
            'client_longitude' => $request->client_longitude,
            'driver_id' => $driver->id,
            'status' => 'en attente'
        ]);

        $driver->update(['status' => 'occupé']);

        return response()->json([
            'status' => 'success',
            'message' => 'Demande envoyée au chauffeur.',
            'data' => $serviceRequest
        ]);
    }

    // ✅ Accepter ou refuser une demande
    public function respondToRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:service_requests,id',
            'response' => 'required|in:accept,refuse',
        ]);

        $serviceRequest = ServiceRequest::find($request->request_id);
        $driver = Auth::user();

        if (!$driver || !$driver instanceof Driver || $driver->id !== $serviceRequest->driver_id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($request->response === 'accept') {
            $serviceRequest->update(['status' => 'accepté']);
            $driver->update(['status' => 'en mission']);
        } else {
            $serviceRequest->update(['status' => 'refusé']);
            $driver->update(['status' => 'disponible']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Réponse enregistrée avec succès.',
            'data' => $serviceRequest
        ]);
    }

    // ✅ Mise à jour du statut du chauffeur
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:disponible,occupé,en mission',
        ]);

        $driver = Auth::user();

        if (!$driver || !$driver instanceof Driver) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $driver->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Statut mis à jour avec succès.',
            'data' => $driver
        ]);
    }

    // ✅ Récupérer le profil du chauffeur connecté
    public function profile($id)
    {
        // Récupère le chauffeur par son ID
        $driver = Driver::find($id);
    
        if (!$driver) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chauffeur non trouvé'
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Profil du chauffeur',
            'data' => $driver
        ]);
    }
    


    // ✅ Récupérer les missions en cours du chauffeur
    public function getMissions()
    {
        $driver = Auth::user();

        if (!$driver || !$driver instanceof Driver) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $missions = ServiceRequest::where('driver_id', $driver->id)
            ->whereIn('status', ['en attente', 'accepté'])
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Missions en cours',
            'data' => $missions
        ]);
    }

    // ✅ Mettre à jour la photo du chauffeur
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $driver = Auth::user();

        if (!$driver || !$driver instanceof Driver) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Supprimer l'ancienne photo si existante
        if ($driver->photo) {
            Storage::disk('public')->delete($driver->photo);
        }

        // Stocker la nouvelle photo
        $photoPath = $request->file('photo')->store('drivers/photos', 'public');

        $driver->update(['photo' => $photoPath]);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo mise à jour avec succès.',
            'data' => $driver
        ]);
    }
}