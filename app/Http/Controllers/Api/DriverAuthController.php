<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;

class DriverAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:drivers,email',
            'phone' => 'nullable|string|max:20|unique:drivers,phone',
            'password' => 'required|string|min:6|confirmed',
            'model' => 'nullable|string|max:255',
            'license_plate' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $driver = Driver::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'password' => Hash::make($request->password),
                'status' => 'disponible',
                'model' => $request->model,
                'license_plate' => $request->license_plate
            ]);

            if (!$driver) {
                DB::rollBack();
                return response()->json(['message' => 'Échec de l\'inscription.'], 500);
            }

            $token = $driver->createToken('authToken')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Inscription réussie',
                'driver' => $driver,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'inscription du chauffeur : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur serveur', 'error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
                'fcm_token' => 'required|string', // Rendre fcm_token obligatoire
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $driver = Driver::where('email', $request->email)->first();

            if (!$driver || !Hash::check($request->password, $driver->password)) {
                return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
            }

            // Supprimer les anciens tokens
            $driver->tokens()->delete();

            // Créer un nouveau token d'authentification
            $token = $driver->createToken('authToken')->plainTextToken;

            // Mettre à jour le FCM token
            $driver->fcm_token = $request->fcm_token;
            $driver->save();

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $driver,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur login driver: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Déconnexion réussie'], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion du chauffeur : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la déconnexion', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateFcmToken(Request $request, $driver_id)
    {
        try {
            $validated = $request->validate([
                'fcm_token' => 'required|string'
            ]);

            $driver = Driver::findOrFail($driver_id);
            $oldToken = $driver->fcm_token;
            $driver->fcm_token = $validated['fcm_token'];
            $driver->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM Token mis à jour avec succès',
                'data' => [
                    'driver_id' => $driver->id,
                    'old_token' => $oldToken,
                    'new_token' => $driver->fcm_token
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour FCM token:', [
                'error' => $e->getMessage(),
                'driver_id' => $driver_id,
                'fcm_token' => $request->fcm_token
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
