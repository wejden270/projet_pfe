<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DemandeController extends Controller
{
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

            // Vérifier si le chauffeur est disponible
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
                'status' => 'required|in:en_attente,acceptee,refusee'
            ]);

            $demande->update([
                'status' => $request->status
            ]);

            // Mettre à jour le statut du chauffeur selon la réponse
            $chauffeur = Driver::find($demande->chauffeur_id);
            if ($chauffeur) {
                $chauffeur->update([
                    'status' => $request->status === 'acceptee' ? 'en mission' : 'disponible'
                ]);
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
}
