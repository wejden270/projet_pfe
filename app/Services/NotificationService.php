<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NotificationService
{
    protected $serverKey;
    protected $firebaseUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('firebase.server_key');
    }

    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        $payload = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json',
        ])->post($this->firebaseUrl, $payload);

        return $response->json();
    }
}
