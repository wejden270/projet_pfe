<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    protected function sendNotification($token, $title, $body, $data = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
                'priority' => 'high',
            ]);

            if (!$response->successful()) {
                Log::error('FCM Error: ' . $response->body());
                return false;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('FCM send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNotificationToDriver($token, $title, $body, $data = [])
    {
        return $this->sendNotification($token, $title, $body, $data);
    }

    public function sendNotificationToClient($token, $title, $body, $data = [])
    {
        return $this->sendNotification($token, $title, $body, $data);
    }
}
