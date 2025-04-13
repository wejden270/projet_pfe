<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\DriverController;
use Illuminate\Http\Request;
use App\Models\Driver; // Importation du modèle Driver

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ces routes sont utilisées pour l'API. Certaines nécessitent une
| authentification via Laravel Sanctum.
*/

// 🟢 Routes publiques (inscription et connexion des clients)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
});

// 🟢 Routes publiques pour l'authentification des chauffeurs
Route::prefix('driver')->group(function () {
    Route::post('register', [DriverAuthController::class, 'register'])->name('api.driver.register');
    Route::post('login', [DriverAuthController::class, 'login'])->name('api.driver.login');
});

// 🔒 Routes protégées nécessitant une authentification avec Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Récupérer l'utilisateur connecté (client ou chauffeur)
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // ✅ Déconnexion des clients
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

    // ✅ Déconnexion des chauffeurs
    Route::post('driver/logout', [DriverAuthController::class, 'logout'])->name('api.driver.logout');

    // ✅ Gestion des utilisateurs (clients)
    Route::apiResource('users', UserController::class)->names('api.users');

    // ✅ Gestion des services
    Route::apiResource('services', ServiceController::class)->names('api.services');

    // ✅ Gestion des localisations
    Route::apiResource('locations', LocationController::class)->names('api.locations');

    // ✅ Routes spécifiques aux chauffeurs authentifiés
    Route::prefix('driver')->group(function () {
        Route::get('profile', [DriverController::class, 'profile'])->name('api.driver.profile'); // ✅ Chauffeur connecté récupère ses infos
        Route::post('update-location', [DriverController::class, 'updateLocation'])->name('api.driver.updateLocation');
        Route::get('missions', [DriverController::class, 'getMissions'])->name('api.driver.missions');

        // ✅ Mettre à jour le statut du chauffeur
        Route::post('update-status', [DriverController::class, 'updateStatus'])->name('api.driver.updateStatus');

        // ✅ Mise à jour du profil (nom, téléphone, photo, etc.)
        Route::post('update', [DriverController::class, 'updateProfile'])->name('api.driver.updateProfile');
    });

    // ✅ Route pour obtenir les chauffeurs à proximité
    Route::get('/drivers/nearby', [DriverController::class, 'getNearbyDrivers'])->name('api.drivers.nearby');
});
Route::get('/w/nearby', [DriverController::class, 'getNearbyDrivers'])->name('api.drivers.nearbyw');

// 🟢 Route publique pour mettre à jour la position des chauffeurs (sans authentification)
Route::post('/chauffeurs/update-location', function (Request $request) {
    $driver = Driver::find($request->driver_id);

    if (!$driver) {
        return response()->json(['error' => 'Chauffeur non trouvé'], 404);
    }

    $driver->latitude = $request->latitude;
    $driver->longitude = $request->longitude;
    $driver->save();

    return response()->json(['message' => 'Position mise à jour']);
});

// 🟢 Route publique pour récupérer les informations d'un chauffeur spécifique par ID
Route::get('/driver/{id}/profile', [DriverController::class, 'profile'])->name('api.driver.profile.byId');
Route::post('/getChauffeursProches', [DriverController::class, 'getNearbyDrivers']);
