<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Driver extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'drivers'; // Assurez-vous que votre table est bien définie

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'photo',
        'latitude',
        'longitude',
        'status',
        'model',          // Type de véhicule du chauffeur
        'license_plate',  // Numéro d'immatriculation du véhicule
        'fcm_token'       // Assurez-vous que fcm_token est bien ici
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Hachage automatique du mot de passe, sauf s'il est déjà haché.
     */
    protected function password(): Attribute
    {
        return new Attribute(
            set: fn($value) => Hash::needsRehash($value) ? Hash::make($value) : $value
        );
    }
}
