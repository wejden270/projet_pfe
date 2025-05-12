<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService;

class DemandeController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $demandes = Demande::with(['client', 'chauffeur'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $demandes
        ]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Nouvelle demande reçue:', $request->all());

            $request->validate([
                'client_id' => 'required|exists:users,id',
                'chauffeur_id' => 'required|exists:drivers,id',
                'client_latitude' => 'required|numeric',
                'client_longitude' => 'required|numeric',
            ]);

            $chauffeur = Driver::find($request->chauffeur_id);
            if (!$chauffeur || $chauffeur->status !== 'disponible') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ce chauffeur n\'est pas disponible.'
                ], 400);
            }

            $demande = Demande::create([
                'client_id' => $request->client_id,
                'chauffeur_id' => $request->chauffeur_id,
                'client_latitude' => $request->client_latitude,
                'client_longitude' => $request->client_longitude,
                'status' => 'en_attente'
            ]);

            if ($chauffeur->fcm_token) {
                $this->firebaseService->sendPushNotification(
                    $chauffeur->fcm_token,
                    'Nouvelle demande de service',
                    'Un client a besoin de vos services',
                    [
                        'request_id' => $demande->id,
                        'type' => 'new_request',
                        'client_latitude' => $request->client_latitude,
                        'client_longitude' => $request->client_longitude
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Demande envoyée avec succès',
                'data' => $demande
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur création demande:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de la demande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $demande = Demande::with(['client', 'chauffeur'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $demande
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Demande non trouvée'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $demande = Demande::findOrFail($id);

            $request->validate([
                'status' => 'required|in:en_attente,acceptee,refusee,annulee'
            ]);

            $demande->update([
                'status' => $request->status
            ]);

            $chauffeur = Driver::find($demande->chauffeur_id);
            if ($chauffeur) {
                $chauffeur->update([
                    'status' => $request->status === 'acceptee' ? 'en mission' : 'disponible'
                ]);
            }

            $client = User::find($demande->client_id);
            if ($client && $client->fcm_token) {
                $title = $request->status === 'acceptee' ? 'Demande acceptée' : 'Demande refusée';
                $message = $request->status === 'acceptee' ? 'Le chauffeur a accepté votre demande' : 'Le chauffeur a refusé votre demande';

                $this->firebaseService->sendPushNotification(
                    $client->fcm_token,
                    $title,
                    $message,
                    [
                        'request_id' => $demande->id,
                        'type' => 'request_response',
                        'status' => $request->status
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Demande mise à jour avec succès',
                'data' => $demande
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour demande:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de la demande'
            ], 500);
        }
    }

    public function getClientDemandes($clientId)
    {
        try {
            $demandes = Demande::with(['client', 'chauffeur'])
                ->where('client_id', $clientId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $demandes
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération demandes client:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    public function cancelDemande($client_id, $demande_id)
    {
        try {
            Log::info('Tentative d\'annulation:', ['client_id' => $client_id, 'demande_id' => $demande_id]);

            $demande = Demande::where('id', $demande_id)
                             ->where('client_id', $client_id)
                             ->first();

            if (!$demande) {
                Log::warning('Demande non trouvée:', ['client_id' => $client_id, 'demande_id' => $demande_id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demande non trouvée ou non autorisée',
                    'details' => 'Aucune demande trouvée avec ces identifiants'
                ], 404);
            }

            if ($demande->status !== 'en_attente') {
                Log::warning('Tentative d\'annulation invalide:', ['status' => $demande->status]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible d\'annuler cette demande',
                    'details' => 'La demande doit être en attente pour pouvoir être annulée. Statut actuel: ' . $demande->status
                ], 400);
            }

            $request = new Request();
            $request->merge(['status' => 'annulee']);
            return $this->update($request, $demande_id);

        } catch (\Exception $e) {
            Log::error('Erreur annulation demande:', [
                'client_id' => $client_id,
                'demande_id' => $demande_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'annulation de la demande',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
