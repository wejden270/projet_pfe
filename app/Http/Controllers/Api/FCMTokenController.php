<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FCMTokenController extends Controller
{
    /**
     * Mettre à jour le token FCM pour un client
     */
    public function updateClientToken(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'fcm_token' => 'required|string'
            ]);

            $user = User::find($request->user_id);
            $user->fcm_token = $request->fcm_token;
            $user->save();

            Log::info('Token FCM client mis à jour', [
                'user_id' => $user->id,
                'token' => substr($request->fcm_token, 0, 10) . '...' // Log partiel pour sécurité
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Token FCM mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour token FCM client:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du token FCM'
            ], 500);
        }
    }

    /**
     * Mettre à jour le token FCM pour un chauffeur
     */
    public function updateDriverToken(Request $request)
    {
        try {
            $request->validate([
                'driver_id' => 'required|exists:drivers,id',
                'fcm_token' => 'required|string'
            ]);

            $driver = Driver::find($request->driver_id);
            $driver->fcm_token = $request->fcm_token;
            $driver->save();

            Log::info('Token FCM chauffeur mis à jour', [
                'driver_id' => $driver->id,
                'token' => substr($request->fcm_token, 0, 10) . '...' // Log partiel pour sécurité
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Token FCM mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour token FCM chauffeur:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du token FCM'
            ], 500);
        }
    }
}
