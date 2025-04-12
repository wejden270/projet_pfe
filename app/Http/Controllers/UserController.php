<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Affiche la liste des utilisateurs
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Affiche le formulaire de création d'un utilisateur
    public function create()
    {
        return view('users.create');
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

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Affiche les détails d'un utilisateur
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // Affiche le formulaire d'édition d'un utilisateur
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
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
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Supprime un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
