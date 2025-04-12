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
    /**
     * Inscription d'un chauffeur
     */
    public function register(Request $request)
    {
        // 🔹 Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:drivers,email',
            'phone' => 'nullable|string|max:20|unique:drivers,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Retourner les erreurs de validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Création du chauffeur avec un statut par défaut "disponible"
            $driver = Driver::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'password' => Hash::make($request->password),
                'status' => 'disponible', // 🚗 Nouveau chauffeur démarre comme "disponible"
            ]);

            if (!$driver) {
                DB::rollBack();
                return response()->json(['message' => 'Échec de l\'inscription.'], 500);
            }

            // 🔹 Génération du token
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

    /**
     * Connexion d'un chauffeur
     */
    public function login(Request $request)
    {
        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🔹 Vérification des identifiants
        $driver = Driver::where('email', $request->email)->first();

        if (!$driver || !Hash::check($request->password, $driver->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        try {
            // 🔹 Suppression des anciens tokens
            $driver->tokens()->delete();

            // 🔹 Génération d'un nouveau token
            $token = $driver->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Connexion réussie',
                'driver' => $driver,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion du chauffeur : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur serveur', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Déconnexion du chauffeur
     */
    public function logout(Request $request)
    {
        try {
            // 🔹 Suppression des tokens de l'utilisateur actuel
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Déconnexion réussie'], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion du chauffeur : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur lors de la déconnexion', 'error' => $e->getMessage()], 500);
        }
    }
}