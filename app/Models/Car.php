<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'make', 'model', 'year', 'license_plate', 'current_location', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}