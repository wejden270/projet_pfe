<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Models\Demande;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        try {
            $demandes = Demande::with(['client', 'chauffeur'])->latest()->get();
            return view('demandes.index', compact('demandes'));
        } catch (\Exception $e) {
            Log::error('Erreur récupération demandes:', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la récupération des demandes');
        }
    }

    public function show(Demande $demande)
    {
        $demande->load(['client', 'chauffeur']);
        return view('demandes.show', compact('demande'));
    }

    public function edit(Demande $demande)
    {
        return view('demandes.edit', compact('demande'));
    }

    public function update(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'status' => 'required|in:en_attente,acceptee,refusee'
        ]);

        $demande->update($validated);

        return redirect()
            ->route('demandes.index')
            ->with('success', 'La demande a été mise à jour avec succès.');
    }

    public function destroy(Demande $demande)
    {
        $demande->delete();
        return redirect()
            ->route('demandes.index')
            ->with('success', 'La demande a été supprimée avec succès.');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'service_type' => 'required',
                'description' => 'required',
                'location' => 'required',
                'address' => 'required'
            ]);

            $demande = Demande::create([
                'user_id' => auth()->id(),
                'driver_id' => $validated['driver_id'],
                'service_type' => $validated['service_type'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'address' => $validated['address'],
                'status' => Demande::STATUS_EN_ATTENTE
            ]);

            $driver = Driver::find($validated['driver_id']);

            Log::info('Sending notification to driver', [
                'driver_id' => $driver->id,
                'fcm_token' => $driver->fcm_token
            ]);

            if ($driver->fcm_token) {
                $this->firebaseService->sendPushNotification(
                    $driver->fcm_token,
                    'Nouvelle demande',
                    'Un client a besoin de vos services !',
                    [
                        'demande_id' => $demande->id,
                        'type' => 'new_request',
                        'client_name' => auth()->user()->name,
                        'service_type' => $demande->service_type,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande envoyée avec succès',
                'data' => $demande
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur création demande: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            Log::info('Starting updateStatus', ['request' => $request->all()]);

            $demande = Demande::with(['user', 'driver'])->findOrFail($id);
            $driver = $demande->driver;
            $status = $request->status;

            $demande->status = $status;
            $demande->save();

            if (in_array($status, ['accepte', 'acceptee'])) {
                $driver->status = 'en_mission';
                $driver->save();
            }

            // Gestion des notifications selon le scénario
            if ($status === 'annulee') {
                // Notification au chauffeur si le client annule
                if ($driver && $driver->fcm_token) {
                    $notificationResult = $this->firebaseService->sendPushNotification(
                        $driver->fcm_token,
                        'Demande annulée',
                        "Le client a annulé sa demande",
                        [
                            'demande_id' => (string)$demande->id,
                            'status' => $status,
                            'type' => 'status_update',
                            'screen' => 'demande_details'
                        ]
                    );
                }
            } elseif ($status === 'refusee') {
                // Notification au client si le chauffeur refuse
                if ($demande->user && $demande->user->fcm_token) {
                    $notificationResult = $this->firebaseService->sendPushNotification(
                        $demande->user->fcm_token,
                        'Demande refusée',
                        "Le chauffeur a refusé votre demande",
                        [
                            'demande_id' => (string)$demande->id,
                            'status' => $status,
                            'type' => 'status_update',
                            'screen' => 'demande_details'
                        ]
                    );
                }
            } elseif ($status === 'acceptee') {
                // Notification au client si le chauffeur accepte
                if ($demande->user && $demande->user->fcm_token) {
                    $notificationResult = $this->firebaseService->sendPushNotification(
                        $demande->user->fcm_token,
                        'Demande acceptée',
                        "Le chauffeur a accepté votre demande",
                        [
                            'demande_id' => (string)$demande->id,
                            'status' => $status,
                            'type' => 'status_update',
                            'screen' => 'demande_details'
                        ]
                    );
                }
            }

            Log::info('Notification result:', ['result' => $notificationResult ?? null]);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'data' => $demande->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('UpdateStatus error:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getStats()
    {
        try {
            $stats = [
                'total_clients' => User::count(),
                'total_drivers' => Driver::count(),
                'total_demandes' => Demande::count(),
                'demandes_par_status' => [
                    'acceptees' => Demande::where('status', 'acceptee')->count(),
                    'refusees' => Demande::where('status', 'refusee')->count(),
                    'annulees' => Demande::where('status', 'annulee')->count(),
                    'en_attente' => Demande::where('status', 'en_attente')->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération stats:', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
}
