<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'car_id',
        'latitude',
        'longitude',
        'timestamp',
    ];

    public $timestamps = true;

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'timestamp' => 'datetime',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->updated_at = now();
        });
    }
}
