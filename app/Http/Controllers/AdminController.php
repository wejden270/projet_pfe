<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Car;
use App\Models\Demande;
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
            $totalDemandes = DB::table('demandes')->count() ?? 0;

            $stats = [
                'total_clients' => $totalClients,
                'total_drivers' => $totalDrivers,
                'total_demandes' => $totalDemandes,
                'total_cars' => $totalCars
            ];

            return view('admin.dashboard', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('admin.dashboard')->with([
                'totalDrivers' => 0,
                'totalClients' => 0,
                'totalCars' => 0,
                'totalDemandes' => 0,
                'error' => 'Erreur de connexion à la base de données: ' . $e->getMessage()
            ]);
        }
    }
}
