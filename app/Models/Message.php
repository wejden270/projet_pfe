<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'sender_type',
        'receiver_type',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    public function receiver()
    {
        return $this->morphTo(__FUNCTION__, 'receiver_type', 'receiver_id');
    }
}
