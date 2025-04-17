<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();

            // Récupérer les statistiques avec gestion des erreurs
            $totalDrivers = DB::table('drivers')->count() ?? 0;
            $totalClients = DB::table('users')->count() ?? 0;
            $totalCars = DB::table('cars')->count() ?? 0;

            return view('admin.dashboard', compact('totalDrivers', 'totalClients', 'totalCars'));

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('admin.dashboard')->with([
                'totalDrivers' => 0,
                'totalClients' => 0,
                'totalCars' => 0,
                'error' => 'Erreur de connexion à la base de données: ' . $e->getMessage()
            ]);
        }
    }
}
