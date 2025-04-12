<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Affiche la liste des utilisateurs
    public function index()
    {
        return User::all();
    }

    // Enregistre un nouvel utilisateur
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return response()->json($user, 201);
    }

    // Affiche les détails d'un utilisateur
    public function show(User $user)
    {
        return $user;
    }

    // Met à jour un utilisateur existant
    public function update(Request $request, User $user)
    {
        // Validation des données d'entrée
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Mise à jour des données de l'utilisateur
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        return response()->json($user);
    }

    // Supprime un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
