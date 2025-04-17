<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\Api\LocationController; // Mise à jour du namespace
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\DriverController; // Mise à jour du namespace
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverAuthController;
use App\Http\Controllers\AdminController;

// Route par défaut pour rediriger vers la page de connexion admin
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Routes pour l'authentification des administrateurs
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::get('register', [AdminAuthController::class, 'showRegistrationForm'])->name('admin.register');
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Tableau de bord (nécessite une authentification admin)
    Route::get('dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('auth:admin');
});

// Routes pour les ressources (CRUD complet)
// Les routes sont protégées par le middleware 'auth:admin'
Route::middleware('auth:admin')->group(function () {
    Route::resource('cars', CarController::class);
    Route::resource('incidents', IncidentController::class);
    Route::resource('locations', LocationController::class); // Route pour LocationController
    Route::resource('users', UserController::class);
    Route::resource('drivers', DriverController::class); // Route pour DriverController
});

// Route pour la page d'accueil après connexion
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth:admin');
// ✅ Déconnexion utilisateur (publique)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ Déconnexion chauffeur (publique)
Route::post('/driver/logout', [DriverAuthController::class, 'logout'])->name('driver.logout');
