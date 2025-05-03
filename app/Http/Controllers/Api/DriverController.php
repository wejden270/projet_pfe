<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;  // Ajout de l'import manquant
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Services\FCMService;

class DriverController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    // ✅ Afficher la liste des chauffeurs
    public function index(Request $request)
    {
        $drivers = Driver::all();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Liste des chauffeurs',
        //     'data' => $drivers
        // ]);

    // 🔹 Vérifier si c'est une requête API
    if ($request->wantsJson()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des chauffeurs',
            'data' => $drivers
        ]);
    }

    // 🔹 Si ce n'est pas une requête API, retourne la vue normale
    return view('drivers.index', compact('drivers'));
    }

    public function create()
{
    return view('drivers.create'); // Assure-toi que `create.blade.php` existe
}

public function store(Request $request)
{
    // 🔹 Valider les champs
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:drivers,email',
        'phone' => 'required|string|max:20',
        'password' => 'required|min:6|confirmed',
        'model' => 'required|string|max:255',
        'license_plate' => 'required|string|max:20'
    ]);

    $driver = Driver::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'],
        'password' => Hash::make($validatedData['password'])
    ]);

    return redirect()->route('drivers.index')->with('success', 'Chauffeur ajouté avec succès !');
}

public function show($id)
{
    $driver = Driver::find($id);

    if (!$driver) {
        return redirect()->route('drivers.index')->with('error', 'Chauffeur non trouvé.');
    }

    return view('drivers.show', compact('driver'));
}

// ✅ Supprimer un chauffeur
public function destroy($id)
{
    $driver = Driver::find($id);

    if (!$driver) {
        return response()->json([
            'status' => 'error',
            'message' => 'Chauffeur non trouvé'
        ], 404);
    }

    $driver->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Chauffeur supprimé avec succès'
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
            [floatval($request->latitude), floatval($request->longitude), floatval($request->latitude)]
        )
        // ->having("distance", "<=", $radius)
        ->where('status', 'disponible')
        ->orderBy("distance", "asc")
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Chauffeurs disponibles à proximité',
            'data' => $drivers,
            'inputLatitude' => $request->latitude,
            'inputLongitude' => $request->longitude,

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
        $client = auth()->user();

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
            'client_id' => $client->id,
            'status' => 'en attente'
        ]);

        // Envoyer notification au chauffeur
        if ($driver->fcm_token) {
            $this->fcmService->sendNotificationToDriver(
                $driver->fcm_token,
                'Nouvelle demande de service',
                'Un client a besoin de vos services',
                [
                    'request_id' => $serviceRequest->id,
                    'type' => 'new_request',
                    'client_latitude' => $request->client_latitude,
                    'client_longitude' => $request->client_longitude
                ]
            );
        }

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
        $client = User::find($serviceRequest->client_id);

        if (!$driver || !$driver instanceof Driver || $driver->id !== $serviceRequest->driver_id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($request->response === 'accept') {
            $serviceRequest->update(['status' => 'accepté']);
            $driver->update(['status' => 'en mission']);
            $title = 'Demande acceptée';
            $message = 'Le chauffeur a accepté votre demande';
        } else {
            $serviceRequest->update(['status' => 'refusé']);
            $driver->update(['status' => 'disponible']);
            $title = 'Demande refusée';
            $message = 'Le chauffeur a refusé votre demande';
        }

        // Envoyer notification au client
        if ($client->fcm_token) {
            $this->fcmService->sendNotificationToClient(
                $client->fcm_token,
                $title,
                $message,
                [
                    'request_id' => $serviceRequest->id,
                    'type' => 'request_response',
                    'status' => $request->response
                ]
            );
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

    public function updateStatusPublic(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disponible,en mission',
        ]);

        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chauffeur non trouvé'
            ], 404);
        }

        $driver->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Statut mis à jour avec succès',
            'data' => $driver
        ]);
    }

    // ✅ Récupérer le profil du chauffeur connecté
    public function profile($id)
    {
        //echo("hello");
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

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:drivers,phone,' . $id,
            'status' => 'required|in:disponible,en mission',
            'model' => 'nullable|string|max:255',
            'license_plate' => 'nullable|string|max:255'
        ]);

        $driver->update($validatedData);

        return redirect()->route('drivers.index')
            ->with('success', 'Chauffeur mis à jour avec succès');
    }

    /**
     * Stocke le FCM token pour un chauffeur
     */
    public function storeFcmToken(Request $request)
    {
        \Log::info('Tentative de stockage FCM token:', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'fcm_token' => 'required|string'
            ]);

            $driver = Driver::findOrFail($validated['driver_id']);

            \Log::info('Avant mise à jour:', [
                'driver_id' => $driver->id,
                'old_token' => $driver->fcm_token,
                'new_token' => $validated['fcm_token']
            ]);

            $driver->fcm_token = $validated['fcm_token'];
            $driver->save();

            \Log::info('Après mise à jour:', [
                'driver_id' => $driver->id,
                'new_token' => $driver->fcm_token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM Token stocké avec succès',
                'data' => [
                    'driver_id' => $driver->id,
                    'fcm_token' => $driver->fcm_token
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur stockage FCM token:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du stockage du FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
