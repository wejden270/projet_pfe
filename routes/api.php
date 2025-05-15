<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\DemandeController;  // Ajout de l'import
use Illuminate\Http\Request;
use App\Models\Driver; // Importation du modèle Driver
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\ChatController;

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

    // Routes pour la mise à jour des tokens FCM
    Route::post('/fcm/token/update', [AuthController::class, 'updateFcmToken']);
    Route::post('/driver/fcm/token/update', [DriverAuthController::class, 'updateFcmToken']);
});
Route::get('/w/nearby', [DriverController::class, 'getNearbyDrivers'])->name('api.drivers.nearbyw');

// 🟢 Route publique pour mettre à jour la position des chauffeurs (sans authentification)
Route::post('/chauffeurs/update-location', function (Request $request) {
    $driver = Driver::find($request->driver_id);

    if (!$driver) {
        return response()->json(['error' => 'Chauffeur non trouvé'], 404);
    }
    echo($request->latitude);
    echo($request->longitude);
    $driver->latitude = $request->latitude;
    $driver->longitude = $request->longitude;
    $driver->save();

    return response()->json(['message' => 'Position mise à jour']);
});

// 🟢 Route publique pour récupérer les informations d'un chauffeur spécifique par ID
Route::get('/driver/{id}/profile', [DriverController::class, 'profile'])->name('api.driver.profile.byId');
Route::post('/getChauffeursProches', [DriverController::class, 'getNearbyDrivers']);
//route publique pour récupérer les chauffeurs
Route::get('/drivers', [DriverController::class, 'index'])->name('api.drivers.index');
//route public pour supprimer un chauffeur
Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('api.drivers.destroy');

// Routes FCM pour les chauffeurs et clients
Route::post('/driver/{driver_id}/fcm-token', [DriverController::class, 'storeFcmToken'])->name('api.driver.fcmToken');
Route::post('/user/{user_id}/fcm-token', [UserController::class, 'storeFcmToken'])->name('api.user.fcmToken');

// Routes publiques pour les demandes (sans authentification)
Route::get('/demandes', [DemandeController::class, 'index'])->name('api.demandes.index');
Route::post('/demandes', [DemandeController::class, 'store'])->name('api.demandes.store');
Route::get('/demandes/{id}', [DemandeController::class, 'show'])->name('api.demandes.show');
Route::put('/demandes/{id}', [DemandeController::class, 'update'])->name('api.demandes.update');
Route::delete('/demandes/{id}', [DemandeController::class, 'destroy'])->name('api.demandes.destroy');
// 🟢 Route publique pour récupérer les demandes d'un client précis
Route::get('/client/{client_id}/demandes', [DemandeController::class, 'getClientDemandes'])->name('api.client.demandes');
Route::post('/client/{client_id}/demandes/{demande_id}/cancel', [DemandeController::class, 'cancelDemande'])->name('api.client.demandes.cancel');
//route publique pour récupérer un mot e masse oblier
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Routes pour gérer les FCM tokens des clients
Route::post('/client/{client_id}/fcm-token', [AuthController::class, 'storeFcmToken'])->name('api.client.fcmToken');

// Route publique pour mettre à jour le statut d'un chauffeur
Route::post('/driver/{id}/update-status', [DriverController::class, 'updateStatusPublic'])->name('api.driver.updateStatus.public');

// Route publique pour mettre à jour la position d'un chauffeur
Route::post('/driver/{id}/update-location', [DriverController::class, 'updateLocationPublic']);

// Route de test pour les notifications
Route::post('/test-notification', [App\Http\Controllers\TestNotificationController::class, 'sendTest']);
//route pour messagerie
Route::post('/send-message-notification', [ChatController::class, 'sendMessageNotification']);
Route::get('/messages', [ChatController::class, 'getMessages']);
Route::post('/test-fcm', [ChatController::class, 'testFcm']);
