<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id', 'make', 'model', 'year', 'license_plate', 'current_location', 'status',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
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
