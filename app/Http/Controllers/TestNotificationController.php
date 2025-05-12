<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestNotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendTest(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
                'title' => 'required|string',
                'body' => 'required|string',
                'data' => 'array|nullable'
            ]);

            $result = $this->firebaseService->sendPushNotification(
                $request->fcm_token,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Test notification failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
