<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    const STATUS_ACCEPTE = 'accepte';
    const STATUS_EN_ATTENTE = 'en_attente';
    const STATUS_ACCEPTEE = 'acceptee';
    const STATUS_REFUSEE = 'refusee';
    const STATUS_ANNULEE = 'annulee';

    public static $statuses = [
        self::STATUS_ACCEPTE,
        self::STATUS_EN_ATTENTE,
        self::STATUS_ACCEPTEE,
        self::STATUS_REFUSEE,
        self::STATUS_ANNULEE
    ];

    protected $fillable = [
        'client_id',
        'chauffeur_id',
        'status',
        'client_latitude',
        'client_longitude',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation avec le client (User)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Relation avec le chauffeur (Driver)
    public function chauffeur()
    {
        return $this->belongsTo(Driver::class, 'chauffeur_id');
    }
}
