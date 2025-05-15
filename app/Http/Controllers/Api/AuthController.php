<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur existe et si le mot de passe correspond
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Créer le token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
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

    /**
     * Met à jour le token FCM de l'utilisateur
     */
    public function updateFcmToken(Request $request)
    {
        \Log::info('Requête updateFcmToken (Client)', [
            'request_data' => $request->all()
        ]);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $oldToken = $user->fcm_token;
            $user->update(['fcm_token' => $request->fcm_token]);

            return response()->json([
                'status' => 'success',
                'message' => 'Token FCM mis à jour avec succès',
                'old_token' => $oldToken,
                'new_token' => $request->fcm_token
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour FCM token: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du token',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function storeFcmToken(Request $request, $client_id)
    {
        $user = User::find($client_id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'Token FCM mis à jour avec succès']);
    }
}
