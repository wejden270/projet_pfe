<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase init error:', ['error' => $e->getMessage()]);
        }
    }

    public function sendPushNotification($token, $title, $body, $data = [])
    {
        try {
            // Format exactement comme dans le test réussi
            $message = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => 'default',
                    'status' => 'done',
                    'screen' => 'notifications'
                ]),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'sound' => 'default'
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ]
                    ]
                ]
            ];

            $response = $this->messaging->send(CloudMessage::fromArray($message));

            Log::info('Notification sent successfully', [
                'token' => $token,
                'response' => $response
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to send notification:', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
            throw $e;
        }
    }

    // Méthode de test
    public function testNotification($token)
    {
        return $this->sendPushNotification(
            $token,
            'Test Notification',
            'Ceci est un test',
            ['type' => 'test']
        );
    }
}
