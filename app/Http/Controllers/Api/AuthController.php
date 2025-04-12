<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Inscription d'un utilisateur
     */
    public function register(Request $request)
    {
        // 🔹 Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' nécessite un champ 'password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction(); // Démarrer une transaction

            // 🔹 Création de l'utilisateur avec hashage du mot de passe
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 🔹 Vérifier si l'utilisateur a bien été créé
            if (!$user) {
                DB::rollBack();
                return response()->json(['message' => 'Échec de l\'inscription.'], 500);
            }

            // 🔹 Création du token pour l'utilisateur
            $token = $user->createToken('authToken')->plainTextToken;

            DB::commit(); // Valider la transaction

            return response()->json([
                'message' => 'Inscription réussie',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::error('Erreur lors de l\'inscription : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur serveur', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request)
    {
        // 🔹 Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // 🔹 Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();

        // 🔹 Si l'utilisateur n'existe pas ou si le mot de passe est incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        try {
            // 🔹 Supprimer les anciens tokens pour éviter les doublons
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            // 🔹 Générer un nouveau token API
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur lors de la génération du token', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        try {
            // 🔹 Supprimer tous les tokens de l'utilisateur
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Déconnexion réussie'], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion : ' . $e->getMessage());

            return response()->json(['message' => 'Erreur lors de la déconnexion', 'error' => $e->getMessage()], 500);
        }
    }
}
