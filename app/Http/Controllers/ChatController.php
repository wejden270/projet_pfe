<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\Message;
use App\Models\User;
use App\Models\Driver;

class ChatController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendMessageNotification(Request $request)
    {
        try {
            $request->validate([
                'sender_id' => 'required',
                'receiver_id' => 'required',
                'message' => 'required',
                'sender_type' => 'required|in:client,driver',
            ]);

            // Si l'expéditeur est un client, le destinataire est un chauffeur et vice versa
            $receiver = $request->sender_type === 'client'
                ? Driver::find($request->receiver_id)  // Client envoie à chauffeur
                : User::find($request->receiver_id);   // Chauffeur envoie à client

            $sender = $request->sender_type === 'client'
                ? User::find($request->sender_id)      // Expéditeur est client
                : Driver::find($request->sender_id);   // Expéditeur est chauffeur

            if (!$receiver || !$receiver->fcm_token) {
                throw new \Exception('Receiver FCM token not found');
            }

            if (!$sender) {
                throw new \Exception('Sender not found');
            }

            $message = CloudMessage::withTarget('token', $receiver->fcm_token)
                ->withNotification(Notification::create(
                    "Message de " . $sender->name,
                    $request->message
                ))
                ->withData([
                    'sender_id' => (string)$request->sender_id,
                    'sender_type' => $request->sender_type,
                    'receiver_type' => $request->sender_type === 'client' ? 'driver' : 'client',
                    'type' => 'chat'
                ]);

            $response = $this->messaging->send($message);

            // Sauvegarder le message
            Message::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'sender_type' => $request->sender_type,
                'receiver_type' => $request->sender_type === 'client' ? 'driver' : 'client'  // Ajout du receiver_type
            ]);

            return response()->json([
                'status' => 'success',
                'message_id' => $response,
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->name,
                    'type' => $request->sender_type
                ],
                'receiver' => [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'type' => $request->sender_type === 'client' ? 'driver' : 'client'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $request->validate([
                'user1_id' => 'required|numeric',
                'user2_id' => 'required|numeric',
            ]);

            $messages = Message::where(function($query) use ($request) {
                $query->where('sender_id', $request->user1_id)
                      ->where('receiver_id', $request->user2_id);
            })->orWhere(function($query) use ($request) {
                $query->where('sender_id', $request->user2_id)
                      ->where('receiver_id', $request->user1_id);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'sender_type' => $message->sender_type,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'read_at' => $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function testFcm(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, [
                "to" => $request->fcm_token,
                "notification" => [
                    "title" => "Test FCM",
                    "body" => "Test message"
                ]
            ]);

            return response()->json([
                'status' => $response->successful() ? 'success' : 'error',
                'response' => $response->json()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

